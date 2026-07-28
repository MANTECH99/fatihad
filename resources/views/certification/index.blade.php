@extends('merchant.layouts.app')

@section('title', 'Obtenir une Certification')

@section('content')
    @php $preselectedShopId = request('shop_id'); @endphp
    <div class="max-w-lg mx-auto mt-10">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Obtenir une Certification</h2>
                </div>
                <a href="{{ route('merchant.dashboard') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>

            <p class="text-gray-500 text-sm mb-6">Choisissez votre niveau de certification et procédez au paiement.</p>

            @if($activeCert)
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    <div>
                        <p class="text-sm text-blue-800 font-medium">Vous avez déjà une certification active</p>
                        <p class="text-xs text-blue-600 mt-1">
                            Vous possédez actuellement : <strong>{{ \App\Services\PlanService::$certifications[$activeCert->plan]['name'] }}</strong>.
                            <br>Si vous en choisissez une nouvelle, l'ancienne sera automatiquement remplacée.
                        </p>
                    </div>
                </div>
            @endif

            <form action="{{ route('certification.pay') }}" method="POST" id="certForm">
                @csrf

                <!-- Entité à certifier -->
                <label class="block text-sm font-medium text-gray-700 mb-1">Sélectionner l'entité à certifier</label>
                <select name="entity" required class="w-full border border-gray-300 rounded-xl px-4 py-3 mb-6 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="" disabled {{ !$preselectedShopId ? 'selected' : '' }}>Choisir une boutique</option>
                    @foreach(auth()->user()->shops as $shop)
                        <option value="{{ $shop->name }}" {{ $preselectedShopId == $shop->id ? 'selected' : '' }}>
                            {{ $shop->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Choix du plan -->
                <label class="block text-sm font-medium text-gray-700 mb-3">Choisir le niveau de certification</label>
                <div class="space-y-3 mb-6">
                    @foreach($plans as $key => $plan)
                        <label class="block cursor-pointer">
                            <input type="radio" name="plan" value="{{ $key }}" class="hidden peer" {{ $loop->first ?  : '' }}>
                            <div class="flex items-center justify-between p-4 border-2 rounded-xl transition-all peer-checked:border-green-600 peer-checked:bg-green-50 hover:bg-gray-50 border-gray-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-500">
                                        <i class="fas fa-{{ $key === 'trusted_seller' ? 'user-check' : ($key === 'entrepreneur' ? 'briefcase' : 'building') }}"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 text-sm md:text-base whitespace-nowrap">{{ $plan['name'] }}</h4>
                                        <p class="text-[10px] md:text-xs text-gray-500 whitespace-nowrap">{{ implode(' • ', $plan['features']) }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-gray-800 text-sm md:text-base whitespace-nowrap">{{ number_format($plan['price'], 0, ',', ' ') }}</span>
                                    <span class="text-[10px] md:text-xs text-gray-400 block whitespace-nowrap">FCFA</span>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

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

                <!-- 🟢 BANDEAU INFORMATION DURÉE 1 AN -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    <div>
                        <p class="text-sm text-blue-800 font-medium">Certification valable 1 an</p>
                        <p class="text-xs text-blue-600 mt-1">Une fois le paiement effectué, votre badge de confiance sera visible sur votre profil et vos produits pendant 365 jours.</p>
                    </div>
                </div>

                <!-- Total -->
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-500">Prix de la certification</span>
                        <span class="font-medium" id="basePriceDisplay">5 000 FCFA</span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-500">Frais de paiement (3.03%)</span>
                        <span class="font-medium text-red-500" id="feeDisplay">152 FCFA</span>
                    </div>
                    <div class="flex justify-between items-center border-t pt-2">
                        <div>
                            <span class="text-sm font-medium text-gray-800">Total</span>
                            <span class="text-xs text-gray-500 block" id="planNameDisplay">Vendeur de Confiance</span>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-bold text-gray-900" id="totalDisplay">5 152</span>
                            <span class="text-sm font-bold text-gray-900"> FCFA</span>
                            <span class="text-xs text-gray-400 block">/an</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('merchant.dashboard') }}" class="flex-1 py-3 text-center border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50">Annuler</a>
                    <button type="submit" class="flex-1 py-3 bg-emerald-600 text-white rounded-xl font-medium hover:bg-emerald-700 flex items-center justify-center gap-2">
                        <i class="fas fa-credit-card"></i> Payer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const fees = @json(array_map(fn($p) => (int) round($p['price'] * 0.03046), $plans));

        document.querySelectorAll('input[name="plan"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const parent = this.closest('label');
                const name = parent.querySelector('h4').innerText;
                const priceText = parent.querySelector('.font-bold').innerText.trim();
                const price = parseInt(priceText.replace(/\s/g, ''));
                const planKey = this.value;
                const fee = fees[planKey] || Math.round(price * 0.03046);
                const total = price + fee;

                document.getElementById('planNameDisplay').innerText = name;
                document.getElementById('basePriceDisplay').innerText = price.toLocaleString() + ' FCFA';
                document.getElementById('feeDisplay').innerText = fee.toLocaleString() + ' FCFA';
                document.getElementById('totalDisplay').innerText = total.toLocaleString();
            });
        });


    </script>
@endsection
