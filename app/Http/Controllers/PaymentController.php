<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shop;
use App\Services\DexpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\CashoutLog;

class PaymentController extends Controller
{
    protected DexpayService $dexpay;

    public function __construct(DexpayService $dexpay)
    {
        $this->dexpay = $dexpay;
    }

    /**
     * Initier le paiement - Redirige vers la page de paiement Dexpay
     */
    public function init(Request $request, Shop $shop, $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('shop_id', $shop->id)
            ->firstOrFail();

        try {
            $result = $this->dexpay->createCheckoutSession($order);

            if ($result['success']) {
                // En sandbox, utiliser sandbox_url si disponible
                $redirectUrl = $result['sandbox_url'] ?? $result['payment_url'];

                if ($redirectUrl) {
                    return redirect()->away($redirectUrl);
                }

                // Fallback : page d'attente
                return view('payment.pending', compact('order', 'result'));
            }

            return redirect()->route('storefront.checkout', $shop->slug)
                ->with('error', $result['message'] ?? 'Paiement échoué');

        } catch (\Exception $e) {
            Log::error('Payment init error', ['error' => $e->getMessage()]);
            return redirect()->route('storefront.checkout', $shop->slug)
                ->with('error', 'Le paiement a échoué. Veuillez réessayer.');
        }
    }

    /**
     * Webhook Callback Dexpay
     * Note: Le header de signature est x-dexchange-signature
     */
    public function callback(Request $request, $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // Vérifier la signature
        $signature = $request->header('x-dexchange-signature');
        $payload = $request->all();

        Log::info('Dexpay Webhook reçu', $payload);

        if ($signature && !$this->dexpay->verifyWebhookSignature($payload, $signature)) {
            Log::warning('Dexpay Webhook - Signature invalide', ['order' => $orderNumber]);
            return response()->json(['success' => false, 'message' => 'Signature invalide'], 401);
        }

        $event = $payload['event'] ?? ($payload['data']['status'] ?? null);
        $data  = $payload['data'] ?? $payload;

        switch ($event) {
            case 'checkout.completed':
                $this->handlePaymentSuccess($order, $data);
                break;

            case 'checkout.failed':
            case 'checkout.cancelled':
                $order->update([
                    'payment_status' => 'failed',
                    'payment_metadata' => array_merge($order->payment_metadata ?? [], [
                        'webhook' => $payload,
                        'failure_reason' => $data['failure_reason'] ?? 'Paiement échoué',
                    ]),
                ]);
                break;

            case 'checkout.refunded':
                $order->update([
                    'payment_status' => 'refunded',
                    'payment_metadata' => array_merge($order->payment_metadata ?? [], [
                        'refund_webhook' => $payload,
                    ]),
                ]);
                break;

            case 'checkout.initiated':
                // Session créée, rien à faire
                break;

            default:
                Log::info('Dexpay Webhook - Événement non géré', ['event' => $event]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Gérer un paiement réussi + cashout automatique
     */
    protected function handlePaymentSuccess(Order $order, array $data): void
    {
        $order->update([
            'payment_status' => 'paid',
            'payment_metadata' => array_merge($order->payment_metadata ?? [], [
                'webhook' => $data,
                'operator' => $data['operator'] ?? null,
                'external_transaction_id' => $data['external_transaction_id'] ?? null,
                'fee_amount' => $data['fee_amount'] ?? 0,
                'merchant_net' => $data['merchant_net'] ?? null,
            ]),
        ]);

        // Synchroniser le client CRM
        $phone = preg_replace('/[^0-9]/', '', $order->customer_phone);
        $customer = \App\Models\Customer::firstOrNew(
            ['shop_id' => $order->shop_id, 'phone' => $phone],
            ['name' => $order->customer_name, 'email' => $order->customer_email]
        );
        $customer->total_orders = $customer->total_orders + 1;
        $customer->total_spent = $customer->total_spent + $order->total;
        $customer->last_order_at = now();

        // Tag automatique
        if ($customer->total_orders >= 10) {
            $customer->tag = 'VIP';
        } elseif ($customer->total_orders >= 3) {
            $customer->tag = 'Régulier';
        } else {
            $customer->tag = 'Nouveau';
        }

        $customer->save();

        // Logger le paiement entrant
        CashoutLog::create([
            'shop_id' => $order->shop_id,
            'admin_id' => null,
            'service_code' => $data['operator'] ?? 'unknown',
            'phone' => $order->customer_phone,
            'amount' => $order->total,
            'external_id' => $data['reference'] ?? $data['external_transaction_id'] ?? null,
            'status' => 'success',
            'response' => json_encode($data),
            'callback_response' => json_encode($data),
        ]);

        // --- Cashout automatique vers le commerçant ---
        $shop = $order->shop;
// Le commerçant choisit où il reçoit l'argent (par défaut, même réseau que le client)
        $payoutMethod = $shop->payout_method ?? $order->payment_method;

        if ($payoutMethod === 'wave') {
            $phone = $shop->wave_number ?? $shop->whatsapp_phone;
        } else {
            $phone = $shop->orange_money_number ?? $shop->whatsapp_phone;
        }

// Si le numéro est vide, fallback sur l'autre réseau
        if (!$phone) {
            $phone = $payoutMethod === 'wave'
                ? $shop->orange_money_number
                : $shop->wave_number;
        }

// Dernier fallback
        if (!$phone) {
            $phone = $shop->whatsapp_phone;
        }

        $cashoutAmount = (int) round($order->subtotal + $order->delivery_fee);

        Log::info('Tentative cashout automatique', [
            'phone' => $phone,
            'amount' => $cashoutAmount,
            'order' => $order->order_number,
            'shop' => $shop->name,
        ]);

        if ($phone && $cashoutAmount >= 250) {
            $operator = $payoutMethod === 'wave' ? 'wave_sn_payout' : 'om_sn_payout';

            try {
                $cashoutResult = $this->dexpay->createPayout([
                    'phone'          => $phone,
                    'amount'         => $cashoutAmount,
                    'currency'       => 'XOF',
                    'operator'       => $operator,
                    'countryISO'     => 'SN',
                    'recipient_name' => $shop->name,
                    'order_number'   => $order->order_number,
                    'shop_id'        => $shop->id,
                ]);

                CashoutLog::create([
                    'shop_id'      => $order->shop_id,
                    'service_code' => $operator,
                    'phone'        => $phone,
                    'amount'       => $cashoutAmount,
                    'external_id'  => $cashoutResult['reference']
                        ?? $cashoutResult['payout_id']
                            ?? ('AUTO-' . $order->order_number . '-' . time()),
                    'status'       => $cashoutResult['success'] ? 'success' : 'failed',
                    'response'     => json_encode($cashoutResult),
                ]);

                Log::info('Auto cashout initié', [
                    'order' => $order->order_number,
                    'shop' => $shop->name,
                    'amount' => $cashoutAmount,
                    'payout_ref' => $cashoutResult['reference'] ?? $cashoutResult['payout_id'] ?? 'N/A',
                ]);
            } catch (\Exception $e) {
                Log::error('Auto cashout failed', [
                    'order' => $order->order_number,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Page succès
     */
    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $shop = $order->shop;  // ← AJOUTER

        // Vérifier le statut réel auprès de Dexpay
        $metadata = $order->payment_metadata ?? [];
        $reference = $metadata['reference'] ?? $orderNumber;

        $session = $this->dexpay->getCheckoutSession($reference);

        if ($session && ($session['data']['status'] ?? '') === 'completed') {
            $order->update(['payment_status' => 'paid']);
        }

        return view('payment.success', compact('order', 'shop'));  // ← AJOUTER $shop
    }

    /**
     * Page échec
     */
    public function failure($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $shop = $order->shop;
        return view('payment.failure', compact('order', 'shop'));
    }


    /**
     * Vérifier le statut du paiement (AJAX polling)
     */
    public function checkStatus($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $metadata = $order->payment_metadata ?? [];
        $reference = $metadata['reference'] ?? $orderNumber;

        $session = $this->dexpay->getCheckoutSession($reference);

        if (!$session) {
            return response()->json(['status' => 'unknown']);
        }

        $dexpayStatus = $session['data']['status'] ?? 'unknown';

        // Mapper statut Dexpay -> statut commande
        $statusMap = [
            'completed'  => 'paid',
            'failed'     => 'failed',
            'cancelled'  => 'failed',
            'refunded'   => 'refunded',
            'initiated'  => 'pending',
            'pending'    => 'pending',
            'processing' => 'pending',
        ];

        $localStatus = $statusMap[$dexpayStatus] ?? 'pending';
        $order->update(['payment_status' => $localStatus]);

        return response()->json([
            'status'      => $dexpayStatus,
            'paid'        => $dexpayStatus === 'completed',
            'redirect'    => $dexpayStatus === 'completed'
                ? route('payment.success', $orderNumber)
                : null,
        ]);
    }
}
