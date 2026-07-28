@extends('merchant.layouts.app')

@section('title', 'Accès Marketplace')

@section('content')
    @php
        use App\Services\PlanService;
        $preselectedShopId = request('shop_id');
    @endphp
    <div class="max-w-lg mx-auto mt-10">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                        <i class="fas fa-store"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Accès Marketplace</h2>
                </div>
                <a href="{{ route('merchant.dashboard') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>

            <p class="text-gray-500 text-sm mb-6">Débloquez l'import et la publication sur la Marketplace Digitale.</p>

            <form action="{{ route('marketplace.pay') }}" method="POST" id="mktForm">
                @csrf

                <label class="block text-sm font-medium text-gray-700 mb-1">Boutique à certifier</label>
                <select name="entity" required class="w-full border border-gray-300 rounded-xl px-4 py-3 mb-6 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="" disabled {{ !$preselectedShopId ? 'selected' : '' }}>Choisir une boutique</option>
                    @foreach(auth()->user()->shops as $shop)
                        <option value="{{ $shop->name }}" {{ $preselectedShopId == $shop->id ? 'selected' : '' }}>
                            {{ $shop->name }}
                        </option>
                    @endforeach
                </select>

                {{-- ✅ CHOIX DES 3 PLANS (Boutons radio design) --}}
                <label class="block text-sm font-medium text-gray-700 mb-3">Choisissez votre plan</label>
                <div class="space-y-3 mb-6">
                    @foreach(PlanService::$marketplacePlans as $key => $plan)
                        <label class="block cursor-pointer">
                            <input type="radio" name="plan" value="{{ $key }}" class="hidden peer" data-price="{{ $plan['price'] }}" data-name="{{ $plan['name'] }}" {{ $loop->first ?  : '' }}>
                            <div class="flex items-center justify-between p-4 border-2 rounded-xl transition-all peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:bg-gray-50 border-gray-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500">
                                        <i class="fas fa-box text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 text-sm md:text-base">{{ $plan['name'] }}</h4>
                                        <p class="text-[10px] md:text-xs text-gray-500">
                                            {{ $plan['max_products'] === -1 ? 'Produits illimités' : 'Jusqu\'à '.$plan['max_products'].' produits' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-gray-800 text-sm md:text-base">{{ number_format($plan['price'], 0, ',', ' ') }}</span>
                                    <span class="text-[10px] md:text-xs text-gray-400 block">FCFA / mois</span>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                <!-- Total -->
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-500">Prix de l'abonnement</span>
                        <span class="font-medium" id="basePriceDisplay">{{ number_format(PlanService::$marketplacePlans[array_key_first(PlanService::$marketplacePlans)]['price'], 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-500">Frais de paiement (3.03%)</span>
                        <span class="font-medium text-red-500" id="feeDisplay">{{ number_format((int) round(PlanService::$marketplacePlans[array_key_first(PlanService::$marketplacePlans)]['price'] * 0.0303), 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between items-center border-t pt-2">
                        <span class="text-sm font-medium text-gray-800">Total</span>
                        <span class="text-xl font-bold text-gray-900" id="totalDisplay">{{ number_format(PlanService::$marketplacePlans[array_key_first(PlanService::$marketplacePlans)]['price'] + (int) round(PlanService::$marketplacePlans[array_key_first(PlanService::$marketplacePlans)]['price'] * 0.0303), 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>

                <input type="hidden" name="plan" id="selectedPlan" value="{{ array_key_first(PlanService::$marketplacePlans) }}">

                <!-- Moyens de paiement acceptés -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3 text-center">Paiement sécurisé via</p>
                    <div class="flex justify-center items-center gap-6">
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/wave.png') }}" alt="Wave" class="h-6 w-auto object-contain">
                            <span class="text-sm font-medium text-green-600">Wave</span>
                        </div>
                        <div class="w-px h-6 bg-gray-300"></div>
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/orange.png') }}" alt="Orange Money" class="h-6 w-auto object-contain">
                            <span class="text-sm font-medium text-orange-600">Orange Money</span>
                        </div>
                    </div>
                </div>

                <!-- 🟢 BANDEAU INFORMATION MENSUEL -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    <div>
                        <p class="text-sm text-blue-800 font-medium">Abonnement mensuel renouvelable</p>
                        <p class="text-xs text-blue-600 mt-1">
                            Choisissez le plan adapté à vos besoins. L'abonnement se renouvelle chaque mois.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('merchant.dashboard') }}" class="flex-1 py-3 text-center border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50">Annuler</a>
                    <button type="submit" class="flex-1 py-3 bg-green-700 text-white rounded-xl font-medium hover:bg-green-800 flex items-center justify-center gap-2">
                        Payer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ✅ JAVASCRIPT POUR METTRE À JOUR LE PRIX ET LE PLAN --}}
    <script>
        const fees = @json(array_map(fn($p) => (int) round($p['price'] * 0.03046), PlanService::$marketplacePlans));

        document.querySelectorAll('input[name="plan"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const price = parseInt(this.dataset.price);
                const name = this.dataset.name;
                const planKey = this.value;
                const fee = fees[planKey] || Math.round(price * 0.03046);
                const total = price + fee;

                document.getElementById('basePriceDisplay').innerText = price.toLocaleString('fr-FR') + ' FCFA';
                document.getElementById('feeDisplay').innerText = fee.toLocaleString('fr-FR') + ' FCFA';
                document.getElementById('totalDisplay').innerText = total.toLocaleString('fr-FR') + ' FCFA';
                document.getElementById('selectedPlan').value = this.value;
            });
        });
    </script>
@endsection
