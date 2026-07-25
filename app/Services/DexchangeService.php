<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DexchangeService
{
    protected $apiKey;
    protected $apiUrl;

    protected $subMerchantId;

    public function __construct()
    {
        $this->apiKey = config('services.dexchange.api_key');
        $this->apiUrl = config('services.dexchange.api_url', 'https://api-m.dexchange.sn/api/v1/transaction/init');
        $this->subMerchantId = config('services.dexchange.sub_merchant_id');
    }

    /**
     * Initier un paiement
     */
    public function initiatePayment(Order $order, $serviceCode)
    {
        // Nettoyer le téléphone (9 chiffres sans +221)
        $phone = preg_replace('/[^0-9]/', '', $order->customer_phone);
        $phone = substr($phone, -9);

        $externalId = str_replace('#', '', $order->order_number) . '-' . time();

        $payload = [
            'externalTransactionId' => $externalId,
            'serviceCode' => $serviceCode,
            'amount' => (int) $order->total,
            'number' => $phone,
            'callBackURL' => route('payment.callback', $order->order_number),
            'successUrl' => route('payment.success', $order->order_number),
            'failureUrl' => route('payment.failure', $order->order_number),
            'sub_merchant_id' => $this->subMerchantId,
        ];

        Log::info('Dexchange - Init payment', $payload);

        $response = Http::withToken($this->apiKey)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->timeout(30)
            ->post($this->apiUrl, $payload);

        $data = $response->json();

        Log::info('Dexchange - Response', ['status' => $response->status(), 'body' => $data]);

        if ($response->successful() && isset($data['transaction']['transactionId'])) {
            $order->update([
                'payment_transaction_id' => $data['transaction']['transactionId'] ?? null,
                'payment_metadata' => [
                    'external_id' => $externalId,
                    'service' => $serviceCode,
                    'dexchange_response' => $data['transaction'] ?? [],
                ],
            ]);

            return [
                'success' => true,
                'transaction_id' => $data['transaction']['transactionId'] ?? null,
                'redirect_url' => $data['transaction']['cashout_url'] ?? null,
            ];
        }

        return [
            'success' => false,
            'message' => $data['message'] ?? 'Paiement échoué',
        ];
    }
}
