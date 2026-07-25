@extends('merchant.layouts.app')

@section('title', 'Paiement - ' . \App\Services\PlanService::$plans[$plan]['name'])
@section('header', 'Paiement')

@section('content')
    <div class="max-w-lg mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border p-8">
            <div class="text-center mb-6">
                <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-sm font-medium">
                    {{ \App\Services\PlanService::$plans[$plan]['name'] }}
                </span>
                <p class="text-4xl font-black text-gray-900 mt-4">{{ number_format($amount, 0, ',', ' ') }} <span class="text-lg font-normal text-gray-400">FCFA</span></p>
                <p class="text-sm text-gray-400 mt-1">par mois</p>
            </div>

            <!-- Détail des frais -->
            @php
                $paymentFee = (int) round($amount * 0.0303);
                $totalAmount = $amount + $paymentFee;
            @endphp

            <div class="bg-gray-50 rounded-xl p-4 mb-6 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Abonnement {{ \App\Services\PlanService::$plans[$plan]['name'] }}</span>
                    <span class="font-medium text-gray-900">{{ number_format($amount, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Frais de paiement mobile</span>
                    <span class="font-medium text-gray-900">{{ number_format($paymentFee, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="border-t border-gray-200 pt-2 flex justify-between">
                    <span class="font-semibold text-gray-900">Total à payer</span>
                    <span class="font-bold text-emerald-600">{{ number_format($totalAmount, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>

            <p class="text-sm text-gray-500 mb-6 text-center">Choisissez votre moyen de paiement</p>

            <div class="space-y-3">
                <a href="{{ route('subscription.pay', ['plan' => $plan, 'method' => 'wave']) }}"
                   class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl hover:border-blue-400 transition group">
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition">
                        <i class="fas fa-wave-square text-blue-500 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">Wave</p>
                        <p class="text-sm text-gray-500">Paiement mobile sécurisé</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-500 transition"></i>
                </a>

                <a href="{{ route('subscription.pay', ['plan' => $plan, 'method' => 'orange_money']) }}"
                   class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl hover:border-orange-400 transition group">
                    <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center group-hover:bg-orange-100 transition">
                        <i class="fas fa-money-bill-wave text-orange-500 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">Orange Money</p>
                        <p class="text-sm text-gray-500">Paiement mobile sécurisé</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-orange-500 transition"></i>
                </a>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mt-4">
                <p class="text-xs text-amber-700">
                    <i class="fas fa-info-circle mr-1"></i>
                    Les frais de paiement mobile (3,03%) couvrent les frais de transaction Dexchange.
                </p>
            </div>

            <p class="text-xs text-gray-400 text-center mt-6">
                <i class="fas fa-lock mr-1"></i> Paiement sécurisé via Dexchange
            </p>
        </div>
    </div>
@endsection
