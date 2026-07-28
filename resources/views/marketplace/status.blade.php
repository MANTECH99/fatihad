@extends('merchant.layouts.app')

@section('title', 'Mon Accès Marketplace')

@section('content')
    <div class="max-w-4xl mx-auto mt-10 px-4">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">

            <!-- Header -->
            <div class="flex items-center justify-between mb-6 border-b pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <i class="fas fa-store"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Mon Accès Marketplace</h2>
                        <p class="text-xs text-gray-400">Statut de votre abonnement</p>
                    </div>
                </div>
                <a href="{{ route('merchant.dashboard') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>

            @if($activeSubs->isNotEmpty())
                @foreach($activeSubs as $activeSub)
                {{-- Abonnement Actif --}}
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-6 mb-8 text-center">
                    <div class="flex justify-center mb-4">
                        <div class="w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 text-4xl">
                            <i class="fas fa-store"></i>
                        </div>
                    </div>

                    <h3 class="text-2xl font-bold text-emerald-800 mb-1">
                        {{ \App\Services\PlanService::$marketplacePlans[$activeSub->plan]['name'] }}
                    </h3>

                    <div class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 px-4 py-1 rounded-full text-sm font-medium mb-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Actif
                    </div>

                    <p class="text-gray-600 text-sm mb-2">
                        <i class="fas fa-store text-gray-400 mr-1"></i>
                        Boutique associée : <span class="font-semibold">
                            @if($activeSub->shop)
                                {{ $activeSub->shop->name }}
                            @else
                                Toutes vos boutiques
                            @endif
                        </span>
                    </p>

                    <p class="text-gray-500 text-sm">
                        <i class="far fa-calendar-alt text-gray-400 mr-1"></i>
                        Expire le : <span class="font-medium">{{ $activeSub->expires_at->format('d/m/Y') }}</span>
                        @if($activeSub->expires_at->isPast())
                            <span class="text-red-500 ml-2">(Expiré)</span>
                        @elseif($activeSub->expires_at->diffInDays(now()) < 7)
                            <span class="text-orange-500 ml-2">(Renouvellement bientôt)</span>
                        @endif
                    </p>

                    {{-- ✅ AFFICHAGE DU PRIX MENSUEL --}}
                    <p class="text-sm text-indigo-600 mt-1">
                        <i class="fas fa-tag mr-1"></i>
                        {{ number_format(\App\Services\PlanService::$marketplacePlans[$activeSub->plan]['price'], 0, ',', ' ') }} FCFA / mois
                    </p>

                    <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('marketplace.index', ['shop_id' => $activeSub->shop_id]) }}"
                           class="inline-flex items-center px-6 py-3 bg-emerald-500 text-white rounded-xl font-medium hover:bg-emerald-600 transition shadow-sm">
                            <i class="fas fa-sync-alt mr-2"></i> Renouveler / Changer
                        </a>
                    </div>
                </div>
                @endforeach
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h4 class="font-medium text-gray-700 mb-2">Ce que vous obtenez :</h4>
                    <ul class="space-y-1 text-sm text-gray-600">
                        @php $planInfo = \App\Services\PlanService::$marketplacePlans[$activeSub->plan]; @endphp

                        {{-- Afficher la limite de produits --}}
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-500"></i>
                            {{ $planInfo['max_products'] === -1 ? 'Produits illimités' : 'Jusqu\'à '.$planInfo['max_products'].' produits' }}
                        </li>

                        @foreach($planInfo['features'] as $feature)
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-emerald-500"></i> {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                </div>

            @else
                {{-- Aucun abonnement actif --}}
                <div class="text-center py-12">
                    <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 text-4xl mx-auto mb-4">
                        <i class="fas fa-store"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Vous n'avez pas d'accès à la Marketplace</h3>
                    <p class="text-gray-500 text-sm mb-6 max-w-sm mx-auto">
                        Abonnez-vous pour accéder à la Marketplace et commencer à vendre et importer des produits.
                    </p>
                    <a href="{{ route('marketplace.index') }}" class="inline-flex items-center px-6 py-3 bg-emerald-500 text-white rounded-xl font-medium hover:bg-emerald-600 transition shadow-sm">
                        <i class="fas fa-plus-circle mr-2"></i> S'abonner
                    </a>
                </div>
            @endif

            {{-- Historique (optionnel) --}}
            @if(isset($history))
                <div class="mt-8 border-t pt-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Historique</h4>
                    <div class="space-y-2">
                        @foreach($history as $sub)
                            <div class="flex items-center justify-between text-sm border-b border-gray-100 pb-2">
                                <div>
                                    <span class="font-medium text-gray-700">{{ \App\Services\PlanService::$marketplacePlans[$sub->plan]['name'] }}</span>
                                    <span class="text-gray-400 text-xs ml-2">
    ({{ $sub->shop ? $sub->shop->name : $sub->entity_name }})
</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-400">{{ $sub->created_at->format('d/m/Y') }}</span>
                                    @if($sub->status === 'active')
                                        <span class="text-emerald-600 text-xs font-medium bg-emerald-50 px-2 py-0.5 rounded">Actif</span>
                                    @else
                                        <span class="text-gray-400 text-xs">{{ ucfirst($sub->status) }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
