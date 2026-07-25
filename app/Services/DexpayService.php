<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DexpayService
{
    protected string $apiKey;
    protected string $apiSecret;
    protected string $apiUrl;
    protected bool $sandbox;

    public function __construct()
    {
        $this->sandbox = config('services.dexpay.sandbox', true);
        $this->apiKey = config('services.dexpay.api_key');
        $this->apiSecret = config('services.dexpay.api_secret');
        $this->apiUrl = $this->sandbox
            ? config('services.dexpay.sandbox_url')
            : config('services.dexpay.api_url');

        Log::info('Dexpay INIT', [
            'sandbox' => $this->sandbox,
            'api_key_start' => substr($this->apiKey, 0, 10) . '...',
            'api_url' => $this->apiUrl,
        ]);
    }

    /**
     * Créer une Checkout Session Dexpay
     */
    public function createCheckoutSession(Order $order): array
    {
        $reference = str_replace('#', '', $order->order_number);

        $payload = [
            'reference'         => $reference,
            'item_name'         => "Commande {$order->order_number}",
            'amount'            => (int) $order->total, // XOF sans décimales
            'currency'          => 'XOF',
            'success_url'       => route('payment.success', $order->order_number),
            'failure_url'       => route('payment.failure', $order->order_number),
            'webhook_url'       => route('payment.callback', $order->order_number),
            'is_one_shot_payment' => true, // pour permettre le remboursement
            'metadata'          => [
                'order_id'    => $order->id,
                'shop_id'     => $order->shop_id,
                'customer_phone' => $order->customer_phone,
                'payment_method' => $order->payment_method, // 'wave' ou 'orange_money'
            ],
        ];

        Log::info('Dexpay - Create checkout session', $payload);

        try {
            $response = Http::withHeaders([
                'x-api-key'    => $this->apiKey,
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->post("{$this->apiUrl}/checkout-sessions", $payload);

            $data = $response->json();

            Log::info('Dexpay - Response', [
                'status' => $response->status(),
                'body'   => $data,
            ]);

            if ($response->successful() && isset($data['data']['payment_url'])) {
                // Mettre à jour la commande avec les infos de session
                $order->update([
                    'payment_transaction_id' => $data['data']['reference'] ?? $reference,
                    'payment_metadata'       => [
                        'reference'          => $reference,
                        'checkout_session_id' => $data['data']['id'] ?? null,
                        'dexpay_response'     => $data['data'],
                    ],
                ]);

                return [
                    'success'      => true,
                    'reference'    => $reference,
                    'payment_url'  => $data['data']['payment_url'],
                    // En sandbox, une sandbox_payment_url peut être présente
                    'sandbox_url'  => $data['data']['sandbox_payment_url'] ?? null,
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Erreur lors de la création de la session de paiement.',
            ];
        } catch (\Exception $e) {
            Log::error('Dexpay - Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Le service de paiement est temporairement indisponible.',
            ];
        }
    }

    /**
     * Récupérer une Checkout Session (pour vérifier le statut)
     */
    public function getCheckoutSession(string $reference): ?array
    {
        try {
            $response = Http::withHeaders(['x-api-key' => $this->apiKey])
                ->timeout(30)
                ->get("{$this->apiUrl}/checkout-sessions/{$reference}");

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Dexpay - Get session error', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Créer un Payout (cashout vers le commerçant)
     */
    public function createPayout(array $payoutData): array
    {
        // Nettoyer le téléphone en format international
        $phone = preg_replace('/[^0-9]/', '', $payoutData['phone']);
        $phone = substr($phone, -9);
        $phone = "+221{$phone}";

        $payload = [
            'amount'             => $payoutData['amount'],
            'currency'           => $payoutData['currency'] ?? 'XOF',
            'destination_phone'  => $phone,
            'destination_details' => [
                'operator'       => $payoutData['operator'] ?? 'wave_sn_payout',
                'countryISO'     => $payoutData['countryISO'] ?? 'SN',
                'recipient_name' => $payoutData['recipient_name'] ?? 'Marchand',
            ],
            'metadata' => [
                'order_number' => $payoutData['order_number'] ?? null,
                'shop_id'      => $payoutData['shop_id'] ?? null,
                'type'         => 'auto_cashout',
            ],
        ];

        Log::info('Dexpay - Create payout', $payload);

        try {
            $response = Http::withHeaders([
                'x-api-key'    => $this->apiKey,
                'x-api-secret' => $this->apiSecret,
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->post("{$this->apiUrl}/payouts", $payload);

            $data = $response->json();

            Log::info('Dexpay - Payout response', [
                'status' => $response->status(),
                'body'   => $data,
            ]);

            if ($response->successful() && in_array($data['status'] ?? '', ['completed', 'PENDING', 'PROCESSING'])) {
                return [
                    'success'       => true,
                    'payout_id'     => $data['id'] ?? null,
                    'reference'     => $data['reference'] ?? null,
                    'status'        => $data['status'] ?? 'PENDING',
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Échec du cashout.',
                'response' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Dexpay - Payout exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Le service de cashout est temporairement indisponible.',
            ];
        }
    }

    /**
     * Vérifier la signature du webhook
     */
    public function verifyWebhookSignature(array $payload, string $signature): bool
    {
        $computed = hash_hmac('sha256', json_encode($payload), $this->apiSecret);
        return hash_equals($computed, $signature);
    }
}
