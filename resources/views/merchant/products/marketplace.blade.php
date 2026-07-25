@extends('merchant.layouts.app')

@section('title', 'Gérer mes produits Marketplace')
@section('header', 'Gérer mes produits Marketplace')

@section('content')

    <div class="max-w-7xl mx-auto px-2 sm:px-4 py-6">
        <!-- Header mobile uniquement (sans sélecteur) -->
        <div class=" bg-white rounded-xl shadow-sm p-5 mb-6 -mt-4">
            <h1 class="text-2xl font-bold text-gray-900">Marketplace FatiHad</h1>
            <p class="text-sm text-gray-500 mt-1">Bienvenue sur FatiHad — Selectionnez les produits que vous souhaitez publier sur la Marketplace </p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2">
        @php
            $hasMarketplace = \App\Models\MarketplaceSubscription::where('shop_id', $shop->id)
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->first();
        @endphp

        @if(!$hasMarketplace)
            {{-- ✅ MESSAGE CLAIR SI PAS D'ABONNEMENT --}}
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-6 text-center">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4 text-emerald-600 text-3xl">
                    <i class="fas fa-store-slash"></i>
                </div>
                <h3 class="text-xl font-bold text-red-emerald mb-2">Accès Marketplace verrouillé</h3>
                <p class="text-emerald-600 text-sm mb-4 max-w-md mx-auto">
                    Vous devez avoir un abonnement <strong>Accès Marketplace</strong> actif pour publier vos produits sur la Marketplace.
                </p>
                <a href="{{ route('marketplace.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition shadow-sm">
                    <i class="fas fa-crown"></i>
                    S'abonner maintenant
                </a>
            </div>
        @else
            {{-- ✅ BANDEAU D'INFORMATION SUR LE PLAN ACTUEL --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                <div class="flex justify-between items-start w-full">
                    <div>
                        @php
                            $planInfo = \App\Services\PlanService::$marketplacePlans[$hasMarketplace->plan] ?? null;
                        @endphp

                        <p class="text-sm text-blue-800 font-medium">
                            Abonnement actif : <strong>{{ $planInfo['name'] ?? 'Accès Marketplace' }}</strong>
                            @if($planInfo)
                                — {{ $planInfo['max_products'] === -1 ? 'Produits illimités' : 'Ajoutez Jusqu\'à '.$planInfo['max_products'].' produits' }}
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-blue-600">
                            Expire le <strong>{{ $hasMarketplace->expires_at->format('d/m/Y') }}</strong>
                            @if($hasMarketplace->expires_at->diffInDays(now()) < 7)
                                <span class="block text-orange-500 text-[10px] mt-0.5">(Renouvellement bientôt)</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- ✅ FORMULAIRE SI ABONNEMENT ACTIF --}}
            <p class="text-gray-500 text-sm mb-4">
                Cochez les produits que vous souhaitez publier sur la Marketplace Seneshop.
            </p>

            <form action="{{ route('merchant.products.updateMarketplace', $shop) }}" method="POST" id="marketplaceForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-2 sm:gap-3">
                    @foreach($products as $product)
                        <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition w-full bg-white">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded flex-shrink-0">
                                @else
                                    <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-800 truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-400">{{ number_format($product->current_price, 0, ',', ' ') }} FCFA</p>
                                </div>
                            </div>

                            <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                <input type="checkbox" name="products[]" value="{{ $product->id }}" class="sr-only peer"
                                    {{ $product->published_on_marketplace ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                <span class="ml-3 text-sm text-gray-600 hidden sm:inline">Publier sur la Marketplace</span>
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition w-full sm:w-auto">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        @endif
        </div>
    </div>
@endsection
