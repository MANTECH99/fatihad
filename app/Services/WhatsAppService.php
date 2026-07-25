<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;
    protected $token;
    protected $phoneNumberId;

    public function __construct()
    {
        $this->apiUrl = 'https://graph.facebook.com/v21.0/';
        $this->token = config('services.whatsapp.token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
    }

    public function sendOrderNotification(Order $order)
    {
        $shopPhone = $this->formatPhone($order->shop->whatsapp_phone);

        // Produits sans sauts de ligne
        $products = '';
        foreach ($order->items as $i => $item) {
            $products .= ($i + 1) . '. ' . $item->quantity . 'x ' . $item->product_name;
            if ($item->options && is_array($item->options)) {
                $opts = [];
                foreach ($item->options as $k => $v) {
                    $opts[] = "$k: $v";
                }
                $products .= ' (' . implode(', ', $opts) . ')';
            }
            $products .= ' - ' . number_format($item->subtotal, 0, ',', ' ') . ' FCFA';
            if ($i < count($order->items) - 1) {
                $products .= ' | ';  // Séparateur au lieu de \n
            }
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $shopPhone,
            'type' => 'template',
            'template' => [
                'name' => 'new_order',
                'language' => ['code' => 'fr'],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $order->order_number],
                            ['type' => 'text', 'text' => $order->customer_name],
                            ['type' => 'text', 'text' => $order->customer_phone],
                            ['type' => 'text', 'text' => $order->customer_address ?? 'Non spécifiée'],
                            ['type' => 'text', 'text' => $products],
                            ['type' => 'text', 'text' => number_format($order->total, 0, ',', ' ') . ' FCFA'],
                            ['type' => 'text', 'text' => $order->getPaymentMethodLabel()],
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendTemplate($shopPhone, $payload, $order);
    }

    public function sendOrderStatusUpdate(Order $order)
    {
        $customerPhone = $this->formatPhone($order->customer_phone);

        $statusMessages = [
            'confirmed' => "✅ Votre commande a été confirmée et sera préparée sous peu.",
            'preparing' => "👨‍🍳 Votre commande est en cours de préparation.",
            'ready' => "📦 Votre commande est prête !",
            'out_for_delivery' => "🛵 Votre commande est en cours de livraison.",
            'delivered' => "🎉 Votre commande a été livrée. Bon appétit !",
            'cancelled' => "❌ Votre commande a été annulée.",
        ];

        $message = $statusMessages[$order->order_status] ?? 'Statut mis à jour.';

        Log::info('Sending status update', [
            'status' => $order->order_status,
            'message' => $message,
        ]);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $customerPhone,
            'type' => 'template',
            'template' => [
                'name' => 'order_status',
                'language' => ['code' => 'fr'],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $order->order_number],
                            ['type' => 'text', 'text' => $order->shop->name],
                            ['type' => 'text', 'text' => $order->getStatusLabel()],
                            ['type' => 'text', 'text' => $message],
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendTemplate($customerPhone, $payload);
    }

    protected function sendTemplate($toPhone, $payload, Order $order = null)
    {
        try {
            $response = Http::withToken($this->token)
                ->timeout(30)
                ->post($this->apiUrl . $this->phoneNumberId . '/messages', $payload);

            Log::info('WhatsApp Response', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            if ($response->successful()) {
                if ($order) {
                    $order->update([
                        'whatsapp_notification_sent' => true,
                        'whatsapp_notified_at' => now()
                    ]);
                }
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    protected function formatPhone($phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        if (str_starts_with($phone, '00')) {
            $phone = '+' . substr($phone, 2);
        }

        if (!str_starts_with($phone, '+')) {
            // Si le numéro commence par 7, 7x ou 7xx, ajouter l'indicatif Sénégal
            if (str_starts_with($phone, '7')) {
                $phone = '+221' . $phone;
            } else {
                $phone = '+' . $phone;
            }
        }

        return $phone;
    }


    public function sendAbandonedCartReminder($phoneNumber, $shopName, $products, $total, $checkoutUrl, $promoCode)
    {
        $customerPhone = $this->formatPhone($phoneNumber);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $customerPhone,
            'type' => 'template',
            'template' => [
                'name' => 'abandoned_cart',
                'language' => ['code' => 'fr'],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $shopName],
                            ['type' => 'text', 'text' => $products],
                            ['type' => 'text', 'text' => number_format($total, 0, ',', ' ')],
                            ['type' => 'text', 'text' => $checkoutUrl],
                            ['type' => 'text', 'text' => $promoCode],
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendTemplate($customerPhone, $payload);
    }
}
