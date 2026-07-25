@extends('layouts.superadmin')

@section('content')

    <style>
        /* Désactiver la surbrillance bleue sur mobile */
        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }

        /* Réactiver la sélection uniquement pour les champs de formulaire */
        input, textarea, select {
            -webkit-user-select: auto;
            user-select: auto;
        }
    </style>
    <div class="max-w-3xl mx-auto p-6">
        @if(session('cashout_url'))
            <script>
                window.onload = function() {
                    window.location.href = "{{ session('cashout_url') }}";
                };
            </script>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                ✅ Redirection vers le paiement en cours...
            </div>
        @endif

        {{-- Solde disponible style Wave/Orange Money --}}
        @if($balance !== null)
            <div class="rounded-2xl shadow-xl p-6 mb-8 text-white relative overflow-hidden"
                 style="background: linear-gradient(135deg, #181A1C 0%, #4D1111 100%);">

                {{-- Cercles décoratifs --}}
                <div class="absolute top-0 right-0 w-40 h-40 rounded-full opacity-10"
                     style="background-color: #E81E25; transform: translate(30%, -30%);"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 rounded-full opacity-10"
                     style="background-color: #E81E25; transform: translate(-30%, 30%);"></div>
                {{--    <div class="absolute top-1/2 left-1/2 w-20 h-20 rounded-full opacity-5"
                        style="background-color: #FFFFFF; transform: translate(-50%, -50%);"></div>--}}

                <div class="relative z-10">
                    {{-- En-tête --}}
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <p class="text-sm opacity-80 font-medium">Solde disponible</p>
                            <p class="text-xs opacity-60">Compte officiel</p>
                        </div>
                        <div class="w-12 h-12 rounded-full flex items-center justify-center cursor-pointer"
                             style="background-color: #E81E25;" onclick="toggleBalance()">
                            <svg id="eye-open-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eye-closed-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Montant principal --}}
                    {{-- Montant principal --}}
                    <div class="text-center my-5">
                        <span class="text-5xl font-black tracking-tight" id="balance-amount">****</span>
                        <span class="text-2xl ml-2 opacity-80 font-light" id="balance-currency"></span>
                    </div>

                    {{-- Barre de progression --}}
                    <div class="w-full bg-white bg-opacity-20 rounded-full h-2.5 mb-4">
                        <div class="h-2.5 rounded-full bg-gradient-to-r from-red-500 to-red-700"
                             style="width: 75%;"></div>
                    </div>

                    {{-- Infos supplémentaires --}}
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-green-400"></span>
                            <span class="text-xs opacity-70">Compte actif</span>
                        </div>
                        <div class="text-xs opacity-70">
                            <span class="bg-white bg-opacity-20 rounded-full px-3 py-1">💳 Abdoul Ahad Gueye</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Message si solde non disponible --}}
        @if($balance === null)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">⚠️</span>
                    <div>
                        <p class="text-yellow-800 font-medium">Solde non disponible</p>
                        <p class="text-yellow-600 text-sm">Impossible de récupérer le solde pour le moment.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Services disponibles --}}
        <div class="grid grid-cols-4 gap-4 mb-8">
            <div class="text-center">
                <img src="{{ asset('images/orange-money.png') }}" alt="Orange Money" class="w-12 h-12 mx-auto mb-2">
                <p class="text-xs font-medium text-gray-600">Orange</p>
            </div>
            <div class="text-center">
                <img src="{{ asset('images/wave.png') }}" alt="Wave" class="w-12 h-12 mx-auto mb-2">
                <p class="text-xs font-medium text-gray-600">Wave</p>
            </div>
            <div class="text-center">
                <img src="{{ asset('images/free-money.png') }}" alt="Free Money" class="w-12 h-12 mx-auto mb-2">
                <p class="text-xs font-medium text-gray-600">Free Money</p>
            </div>
            <div class="text-center">
                <img src="{{ asset('images/wizalls.png') }}" alt="Wizall" class="w-12 h-12 mx-auto mb-2">
                <p class="text-xs font-medium text-gray-600">Wizall</p>
            </div>
        </div>

        {{-- Résumé rapide --}}
        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Envois</p>
                <p class="text-xl font-bold" style="color: #4D1111">{{ $logs->whereIn('service_code', ['wave_sn_payout', 'orange_money_sn_payout', 'free_money_sn_payout'])->count() }}</p>
                <p class="text-xs text-gray-400">Total</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Réceptions</p>
                <p class="text-xl font-bold" style="color: #10B981">{{ $logs->whereIn('service_code', ['wave_sn', 'orange_money_sn', 'WAVE_SN_CASHOUT', 'OM_SN_CASHOUT'])->count() }}</p>
                <p class="text-xs text-gray-400">Total</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Frais</p>
                <p class="text-xl font-bold" style="color: #E81E25">1.5%</p>
                <p class="text-xs text-gray-400">Par transaction</p>
            </div>
        </div>

        {{-- Formulaire --}}
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold mb-4 flex items-center" style="color: #181A1C">
        <span class="w-8 h-8 rounded-full flex items-center justify-center mr-2 text-white text-sm"
              style="background-color: #4D1111">1</span>
                Effectuer un retrait
            </h2>

            <form action="{{ route('admin.cashout.initiate') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1" style="color: #181A1C">
                        Service de retrait
                    </label>
                    <select name="operator" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="">-- Sélectionner un service --</option>
                        <option value="wave_sn_payout">🔵 Wave (1.5%)</option>
                        <option value="om_sn_payout">🟠 Orange Money (1.4%)</option>
                        <option value="free_money_sn_payout">🟢 Free Money (1.5%)</option>
                    </select>
                    @error('operator')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1" style="color: #181A1C">
                        Numéro de téléphone
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                           placeholder="77 234 56 87"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <p class="text-xs text-gray-400 mt-1">Format sénégalais : 77 XXX XX XX</p>
                    @error('phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium mb-1" style="color: #181A1C">
                        Montant (FCFA)
                    </label>
                    <div class="relative">
                        <input type="number" name="amount" value="{{ old('amount') }}" required min="250" step="1"
                               placeholder="5000"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <span class="absolute right-3 top-2.5 text-gray-400 font-medium">FCFA</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Minimum : 250 FCFA</p>

                    {{-- Détail des frais --}}
                    <div id="frais-detail" class="mt-3 p-3 bg-gray-50 rounded-lg hidden">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-500">Montant envoyé</span>
                            <span class="font-medium" id="montant-envoye">0 FCFA</span>
                        </div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-500">Frais (1.5%)</span>
                            <span class="font-medium text-red-500" id="frais-montant">0 FCFA</span>
                        </div>
                        <div class="flex justify-between text-xs font-bold pt-1 border-t border-gray-200">
                            <span>Total débité</span>
                            <span id="total-debite" style="color: #4D1111">0 FCFA</span>
                        </div>
                        @if($balance !== null)
                            <div class="flex justify-between text-xs mt-1 pt-1 border-t border-gray-200">
                                <span class="text-gray-500">Solde après envoi</span>
                                <span id="solde-apres" class="font-medium" style="color: #4D1111">{{ number_format($balance, 0) }} FCFA</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium mb-1" style="color: #181A1C">
                        Code 2FA
                    </label>
                    <input type="text" name="code_2fa" required
                           placeholder="123 456"
                           maxlength="6"
                           class="w-full text-center text-2xl tracking-widest border-2 border-gray-300 rounded-lg py-3 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           style="letter-spacing: 8px;">
                    <p class="text-xs text-gray-400 mt-1">Entrez le code de votre application d'authentification</p>
                    @error('code_2fa')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full text-white py-3 rounded-lg font-bold transition duration-300 transform hover:scale-105"
                        style="background-color: #E81E25">
                    💸 Effectuer le retrait
                </button>
            </form>
        </div>
        {{-- Historique style Wave --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4 flex items-center" style="color: #181A1C">
        <span class="w-8 h-8 rounded-full flex items-center justify-center mr-2 text-white text-sm"
              style="background-color: #4D1111">📋</span>
                Historique
            </h2>

            {{-- Onglets --}}
            <div class="flex border-b mb-4 space-x-12">
                <button onclick="showTab('envois')" id="tab-envois"
                        class="px-4 py-2 text-sm font-medium border-b-2 border-red-500 text-red-500">
                    📤 Envois
                </button>
                <button onclick="showTab('receptions')" id="tab-receptions"
                        class="px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                    📥 Réceptions
                </button>
            </div>

            @php
                $envois = $logs->whereIn('service_code', ['wave_sn_payout', 'orange_money_sn_payout', 'free_money_sn_payout']);
                $receptions = $logs->whereIn('service_code', ['wave_sn', 'orange_money_sn', 'WAVE_SN_CASHOUT', 'OM_SN_CASHOUT', 'FM_SN_CASHOUT', 'WIZALL_SN_CASHOUT']);
            @endphp

            {{-- Envois --}}
            <div id="tab-content-envois">
                @if($envois->count() > 0)
                    <div class="space-y-1">
                        @foreach($envois as $log)
                            @include('admin.cashout.partials.log-item', ['log' => $log, 'isCashin' => true])
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">Aucun envoi</div>
                @endif
            </div>

            {{-- Réceptions --}}
            <div id="tab-content-receptions" class="hidden">
                @if($receptions->count() > 0)
                    <div class="space-y-1">
                        @foreach($receptions as $log)
                            @include('admin.cashout.partials.log-item', ['log' => $log, 'isCashin' => false])
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">Aucune réception</div>
                @endif
            </div>
        </div>

        <script>
            function showTab(tab) {
                document.getElementById('tab-content-envois').classList.add('hidden');
                document.getElementById('tab-content-receptions').classList.add('hidden');
                document.getElementById('tab-envois').classList.remove('border-red-500', 'text-red-500');
                document.getElementById('tab-envois').classList.add('border-transparent', 'text-gray-500');
                document.getElementById('tab-receptions').classList.remove('border-red-500', 'text-red-500');
                document.getElementById('tab-receptions').classList.add('border-transparent', 'text-gray-500');

                if (tab === 'envois') {
                    document.getElementById('tab-content-envois').classList.remove('hidden');
                    document.getElementById('tab-envois').classList.add('border-red-500', 'text-red-500');
                    document.getElementById('tab-envois').classList.remove('border-transparent', 'text-gray-500');
                } else {
                    document.getElementById('tab-content-receptions').classList.remove('hidden');
                    document.getElementById('tab-receptions').classList.add('border-red-500', 'text-red-500');
                    document.getElementById('tab-receptions').classList.remove('border-transparent', 'text-gray-500');
                }
            }
        </script>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        const amountInput = document.querySelector('input[name="amount"]');
        const fraisDetail = document.getElementById('frais-detail');
        const balance = {{ $balance ?? 0 }};

        amountInput?.addEventListener('input', function() {
            const amount = parseFloat(this.value) || 0;

            if (amount >= 250) {
                fraisDetail.classList.remove('hidden');

                const frais = Math.round(amount * 0.015);
                const total = amount + frais;
                const soldeApres = balance - total;

                document.getElementById('montant-envoye').textContent = amount.toLocaleString() + ' FCFA';
                document.getElementById('frais-montant').textContent = frais.toLocaleString() + ' FCFA';
                document.getElementById('total-debite').textContent = total.toLocaleString() + ' FCFA';

                const soldeEl = document.getElementById('solde-apres');
                if (soldeEl) {
                    soldeEl.textContent = soldeApres.toLocaleString() + ' FCFA';
                    soldeEl.style.color = soldeApres < 0 ? '#E81E25' : '#4D1111';
                }
            } else {
                fraisDetail.classList.add('hidden');
            }
        });

        let balanceVisible = false;
        const realBalance = {{ $balance ?? 0 }};

        function toggleBalance() {
            balanceVisible = !balanceVisible;
            const amountEl = document.getElementById('balance-amount');
            const currencyEl = document.getElementById('balance-currency');
            const eyeOpen = document.getElementById('eye-open-icon');
            const eyeClosed = document.getElementById('eye-closed-icon');

            if (balanceVisible) {
                amountEl.textContent = realBalance.toLocaleString();
                currencyEl.textContent = 'FCFA';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            } else {
                amountEl.textContent = '****';
                currencyEl.textContent = '';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            }
        }
    </script>
@endsection
