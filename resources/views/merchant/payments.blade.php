@extends('merchant.layouts.app')

@section('title', 'Paiements')
@section('header', 'Paiements')

@section('content')

    @php
        $userPlan = auth()->user()->plan ?? 'free';
    @endphp

    @if($userPlan === 'free')
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.4); backdrop-filter: blur(4px);">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 p-8 max-w-md w-full text-center relative z-50">

                {{-- ❌ Bouton X pour fermer --}}
                <button onclick="this.closest('.fixed').remove(); document.querySelector('[x-data]').classList.remove('blur-sm', 'pointer-events-none', 'select-none')"
                        class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times"></i>
                </button>

                <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4 text-amber-600 text-4xl">
                    <i class="fas fa-crown"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Fonctionnalité Premium</h2>
                <p class="text-gray-500 mb-4">
                    Passez à un plan <strong>payant</strong> pour débloquer Suivi des paiements.
                </p>

                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <p class="text-sm text-gray-500">Votre plan actuel</p>
                    @php
                        $planKey = auth()->user()->plan ?? 'free';
                        $planName = \App\Services\PlanService::$plans[$planKey]['name'] ?? 'Gratuit';
                    @endphp
                    <p class="text-xl font-bold text-gray-800 uppercase">
                        {{ $planName }}
                    </p>
                </div>

                <a href="{{ route('subscription.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 text-white rounded-xl font-medium hover:bg-emerald-600 transition shadow-sm">
                    <i class="fas fa-rocket"></i>
                    Passer au plan Professionnel
                </a>
            </div>
        </div>
    @endif
    <div class="space-y-6 {{ $userPlan === 'free' ? 'blur-sm pointer-events-none select-none' : '' }}">
        <p class="text-sm text-gray-500">Suivi des paiements mobile money</p>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
            {{-- Total encaissé --}}
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex items-center justify-between shadow-sm lg:shadow-none">
                <div>
                    <p class="text-sm text-gray-500">Total encaissé</p>
                    <p class="mt-1 text-sm lg:text-2xl font-bold text-gray-900">
                        {{ number_format($totalEncaissé, 0, ',', ' ') }} FCFA
                    </p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-full bg-emerald-100 flex items-center justify-center">
                    <i data-lucide="wallet" class="text-emerald-600 w-5 h-5 lg:w-6 lg:h-6"></i>
                </div>
            </div>

            {{-- Wave --}}
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex items-center justify-between shadow-sm lg:shadow-none">
                <div>
                    <p class="text-sm text-gray-500">Wave</p>
                    <p class="mt-1 text-base lg:text-2xl font-bold text-blue-600">
                        {{ number_format($totalWave, 0, ',', ' ') }} FCFA
                    </p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-full bg-blue-50 flex items-center justify-center">
                    <img src="{{ asset('images/wave.png') }}" alt="Wave" class="w-6 h-6 lg:w-8 lg:h-8 object-contain">
                </div>
            </div>

            {{-- Orange Money --}}
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex items-center justify-between shadow-sm lg:shadow-none">
                <div>
                    <p class="text-sm text-gray-500">Orange Money</p>
                    <p class="mt-1 text-base lg:text-2xl font-bold text-orange-500">
                        {{ number_format($totalOM, 0, ',', ' ') }} FCFA
                    </p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-full bg-orange-50 flex items-center justify-center">
                    <img src="{{ asset('images/orange-money.png') }}" alt="Orange Money" class="w-6 h-6 lg:w-8 lg:h-8 object-contain">
                </div>
            </div>

            {{-- À la livraison --}}
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex items-center justify-between shadow-sm lg:shadow-none">
                <div>
                    <p class="text-sm text-gray-500">À la livraison</p>
                    <p class="mt-1 text-sm lg:text-2xl font-bold text-gray-700">{{ number_format($totalCOD, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-full bg-gray-100 flex items-center justify-center">
                    <i data-lucide="truck" class="text-gray-600 w-5 h-5 lg:w-6 lg:h-6"></i>
                </div>
            </div>
        </div>

        <!-- Historique des paiements -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold">Historique des paiements</h2>
            </div>

            <div class="p-6">
                @if($paiements->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($paiements as $paiement)
                            <div class="flex items-center justify-between border-b pb-3">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $paiement->order_number }}</p>
                                    <p class="text-sm text-gray-500">{{ $paiement->created_at->format('d/m/Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900">{{ number_format($paiement->total, 0, ',', ' ') }} FCFA</p>
                                    {{-- Remplacer l'affichage du mode de paiement dans l'historique --}}
                                    <p class="text-sm
    {{ $paiement->payment_method === 'wave' ? 'text-blue-500' : '' }}
    {{ $paiement->payment_method === 'orange_money' ? 'text-orange-500' : '' }}
    {{ $paiement->payment_method === 'cash_on_delivery' ? 'text-gray-500' : '' }}">
                                        {{ $paiement->payment_method === 'wave' ? 'Wave' : '' }}
                                        {{ $paiement->payment_method === 'orange_money' ? 'Orange Money' : '' }}
                                        {{ $paiement->payment_method === 'cash_on_delivery' ? 'Livraison' : '' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $paiements->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-credit-card text-5xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Aucun paiement reçu.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
