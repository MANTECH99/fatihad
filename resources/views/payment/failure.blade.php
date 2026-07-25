@extends('layouts.storefront')

@section('title', 'Paiement échoué')

@section('content')
    <div class="max-w-lg mx-auto px-4 py-16 text-center">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-times-circle text-4xl text-red-500"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Paiement échoué</h1>
            <p class="text-gray-500 mb-6">Une erreur est survenue lors du paiement.</p>
            <a href="{{ route('storefront.show', $order->shop->slug) }}" class="inline-block bg-emerald-500 text-white px-6 py-3 rounded-xl font-medium">
                Réessayer
            </a>
        </div>
    </div>
@endsection
