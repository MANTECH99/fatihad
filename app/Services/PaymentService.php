<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $gateway;

    public function __construct()
    {
        // Récupère la passerelle active (Dexchange quand tu l'auras configurée)
        $this->gateway = PaymentGateway::where('is_active', true)->first();
    }

    public function isEnabled()
    {
        return $this->gateway !== null && $this->gateway->is_active;
    }

    public function initiatePayment(Order $order, $method)
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'message' => 'Paiement en ligne non disponible pour le moment.'
            ];
        }

        // Préparé pour Dexchange API
        return match($this->gateway->name) {
            'dexchange' => $this->processDexchangePayment($order, $method),
            default => [
                'success' => false,
                'message' => 'Passerelle de paiement non supportée.'
            ],
        };
    }

    protected function processDexchangePayment(Order $order, $method)
    {
        // TODO: Intégrer Dexchange API ici
        // $config = $this->gateway->config;
        // $apiKey = $config['api_key'];
        // $endpoint = $this->gateway->is_test_mode ? $config['test_endpoint'] : $config['live_endpoint'];

        Log::info('Payment initiation prepared', [
            'gateway' => 'dexchange',
            'order_id' => $order->id,
            'amount' => $order->total,
            'method' => $method,
            'test_mode' => $this->gateway->is_test_mode
        ]);

        return [
            'success' => false,
            'message' => 'Paiement en ligne en cours d\'intégration. Veuillez utiliser le paiement à la livraison.',
            'redirect_url' => null
        ];
    }

    public function processCallback(array $data)
    {
        Log::info('Payment callback received', $data);

        // TODO: Traiter le callback de Dexchange
        return [
            'success' => false,
            'message' => 'Callback processing not yet implemented'
        ];
    }
}
