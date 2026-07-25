<?php

namespace App\Console\Commands;

use App\Models\AbandonedCart;
use Illuminate\Console\Command;

class SendAbandonedCartReminders extends Command
{
    protected $signature = 'carts:remind';
    protected $description = 'Envoie les relances WhatsApp pour les paniers abandonnés';

    public function handle()
    {
        $whatsapp = app(\App\Services\WhatsAppService::class);

        $carts = AbandonedCart::where('reminder_sent', false)
            ->where('recovered', false)
            //->where('created_at', '<', now()->subHours(2))
            ->where('created_at', '<', now()->subMinutes(2)) // Au lieu de subHours(2)
            ->where('created_at', '>', now()->subDays(3))
            ->get();

        foreach ($carts as $cart) {
            $shop = $cart->shop;

// APRÈS (récupère le vrai nom depuis la base)
            $products = collect($cart->cart_items)->map(function ($item) use ($shop) {
                $product = \App\Models\Product::find($item['product_id']);
                $name = $product ? $product->name : 'Produit';
                $qty = $item['quantity'] ?? 1;
                return $qty . 'x ' . $name;
            })->implode(', ');

            $checkoutUrl = route('storefront.checkout', $shop->slug);
            $promoCode = 'VITE10';

            // Envoyer via WhatsApp
            $whatsapp->sendAbandonedCartReminder(
                $cart->customer_phone,
                $shop->name,
                $products,
                $cart->total,
                $checkoutUrl,
                $promoCode
            );

            $cart->update([
                'reminder_sent' => true,
                'reminder_sent_at' => now(),
            ]);
        }

        $this->info("{$carts->count()} relances envoyées.");
    }
}
