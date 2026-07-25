@extends('layouts.storefront')

@section('title', 'Paiement réussi')

@section('content')
    <div class="max-w-lg mx-auto px-4 py-16 text-center">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check-circle text-4xl text-green-500"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Paiement réussi !</h1>
            <p class="text-gray-500 mb-4">Commande {{ $order->order_number }}</p>
            <p class="text-3xl font-bold text-emerald-600 mb-6">{{ number_format($order->total, 0, ',', ' ') }} FCFA</p>
            <a href="{{ route('storefront.show', $order->shop->slug) }}" class="inline-block bg-emerald-500 text-white px-6 py-3 rounded-xl font-medium">
                Retour à la boutique
            </a>
        </div>
    </div>
@endsection
