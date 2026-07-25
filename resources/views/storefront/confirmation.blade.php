{{-- resources/views/storefront/confirmation.blade.php --}}
@extends('layouts.storefront')

@section('title', 'Commande confirmée')

@section('content')

    @if($shop->facebook_pixel_id && isset($order))
        <script>
            fbq('track', 'Purchase', {
                content_ids: [{{ $order->items->pluck('product_id')->implode(',') }}],
                content_type: 'product',
                value: {{ $order->total }},
                currency: 'XOF'
            });
        </script>
    @endif
    <div class="max-w-2xl mx-auto px-4 py-12 text-center">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check text-4xl text-green-500"></i>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-2">Commande confirmée !</h1>
            <p class="text-gray-600 mb-6">Votre commande a été envoyée avec succès.</p>

            <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">N° Commande</span>
                        <span class="font-bold">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Date</span>
                        <span>{{ $order->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total</span>
                        <span class="font-bold text-emerald-600">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Paiement</span>
                        <span>{{ $order->getPaymentMethodLabel() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Statut</span>
                        <span class="text-yellow-600 font-medium">{{ $order->getStatusLabel() }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-blue-800">
                    <i class="fab fa-whatsapp mr-2"></i>
                    Le commerçant a reçu votre commande et vous contactera bientôt pour confirmer la livraison.
                </p>
            </div>

            <div class="space-y-3">
                <a href="{{ route('storefront.show', $shop->slug) }}"
                   class="block w-full bg-emerald-500 text-white py-3 rounded-lg font-medium hover:bg-emerald-600 transition">
                    <i class="fas fa-utensils mr-2"></i> Retour à la boutique
                </a>

                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $shop->whatsapp_phone) }}"
                   target="_blank"
                   class="block w-full bg-green-500 text-white py-3 rounded-lg font-medium hover:bg-green-600 transition">
                    <i class="fab fa-whatsapp mr-2"></i> Contacter sur WhatsApp
                </a>
            </div>
        </div>
    </div>
@endsection
