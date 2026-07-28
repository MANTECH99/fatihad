@extends('merchant.layouts.app')

@section('title', 'Mes retraits - ' . $shop->name)

@section('header', 'Mes retraits - ' . $shop->name)

@section('content')

    <style>
        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }
        input, textarea, select {
            -webkit-user-select: auto;
            user-select: auto;
        }
        main {
            padding-top: 0 !important;
        }
    </style>

    <div class="max-w-full lg:max-w-3xl mx-auto px-0 lg:px-6 py-6">
        <h1 class="text-xl font-bold text-gray-800 mb-4 text-center lg:text-left">
            📊 Suivez l'historique de vos revenus
        </h1>

        {{-- Solde disponible style Wave/Orange Money --}}
        <div class="rounded-2xl shadow-xl p-6 mb-8 text-white relative overflow-hidden"
             style="background: linear-gradient(135deg, #064E3B 0%, #059669 100%);">

            <div class="absolute top-0 right-0 w-40 h-40 rounded-full opacity-10"
                 style="background-color: #10B981; transform: translate(30%, -30%);"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 rounded-full opacity-10"
                 style="background-color: #10B981; transform: translate(-30%, 30%);"></div>

            <div class="relative z-10">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <p class="text-sm opacity-80 font-medium">Solde disponible</p>
                        <p class="text-xs opacity-60">Compte {{ $shop->name }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-full flex items-center justify-center cursor-pointer"
                         style="background-color: #059669;" onclick="toggleBalance()">
                        <svg id="eye-open-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg id="eye-closed-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </div>
                </div>

                <div class="text-center my-5">
                    <span class="text-5xl font-black tracking-tight" id="balance-amount">****</span>
                    <span class="text-2xl ml-2 opacity-80 font-light" id="balance-currency"></span>
                </div>

                <div class="w-full bg-white bg-opacity-20 rounded-full h-2.5 mb-4">
                    <div class="h-2.5 rounded-full bg-gradient-to-r from-green-400 to-green-600" style="width: 75%;"></div>
                </div>

                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-green-400"></span>
                        <span class="text-xs opacity-70">Compte actif</span>
                    </div>
                    <div class="text-xs opacity-70">
                        <span class="bg-white bg-opacity-20 rounded-full px-3 py-1">💳 {{ $shop->name }}</span>
                    </div>
                </div>
            </div>
        </div>

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
        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Montants reçus</p>
                <p class="text-xl font-bold text-emerald-600">{{ number_format($totalRecu, 0) }} FCFA</p>
                <p class="text-xs text-gray-400">{{ $nombreTransactions }} transaction(s)</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Frais</p>
                <p class="text-xl font-bold text-red-500">1.5%</p>
                <p class="text-xs text-gray-400">Par transaction</p>
            </div>
        </div>

        {{-- Historique des montants reçus --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4 flex items-center text-gray-800">
                <span class="w-8 h-8 rounded-full flex items-center justify-center mr-2 text-white text-sm bg-emerald-600">📥</span>
                Montants reçus
            </h2>

            @if($allPayments->count() > 0)
                <div class="space-y-1">
                    @foreach($allPayments as $payment)
                        <div class="flex justify-between items-center py-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50 px-2 rounded"
                             onclick="openModal('payment-{{ $payment->id }}')">
                            <div>
                                @php
                                    $montantRecu = $payment->amount;
                                    $clientPhone = $payment->client_phone ?? 'Client';
                                @endphp
                                <p class="font-medium text-sm">De &nbsp;{{ \App\Helpers\PhoneHelper::formatLocal($clientPhone) }}</p>
                                <p class="text-xs text-gray-400">{{ $payment->created_at->format('d F Y') }} {{ $payment->created_at->format('h:i A') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold" style="color: #10B981">+{{ number_format($montantRecu, 0) }} FCFA</p>
                            </div>
                        </div>

                        {{-- Modal --}}
                        <div id="payment-{{ $payment->id }}" class="hidden fixed inset-0 z-50 bg-white overflow-y-auto md:max-w-3xl md:mx-auto md:relative md:inset-auto">
                            <div class="sticky top-0 bg-white border-b z-10">
                                <div class="flex items-center p-4">
                                    <button onclick="closeModal('payment-{{ $payment->id }}')" class="mr-4">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                    </button>
                                    <p class="font-semibold text-lg">Détails du montant reçu</p>
                                </div>
                            </div>

                            <div class="p-6">
                                <div class="text-center mb-4">
                                    <p class="text-4xl font-black" style="color: #10B981">+{{ number_format($montantRecu, 0) }} FCFA</p>
                                    <p class="text-gray-500 mt-2">De {{ \App\Helpers\PhoneHelper::formatLocal($clientPhone) }}</p>
                                </div>

                                <div class="space-y-4">
                                    <div class="flex justify-between py-3 border-b">
                                        <span class="text-gray-500">Montant reçu</span>
                                        <span class="font-medium" style="color: #10B981">{{ number_format($montantRecu, 0) }} FCFA</span>
                                    </div>
                                    <div class="flex justify-between py-3 border-b">
                                        <span class="text-gray-500">Numéro client</span>
                                        <span class="font-medium">{{ \App\Helpers\PhoneHelper::formatLocal($clientPhone) }}</span>
                                    </div>
                                    <div class="flex justify-between py-3 border-b">
                                        <span class="text-gray-500">Type</span>
                                        <span class="font-medium text-green-600">📥 Montant reçu</span>
                                    </div>
                                    <div class="flex justify-between py-3 border-b">
                                        <span class="text-gray-500">Statut</span>
                                        <span class="font-medium text-green-600">✅ Effectué</span>
                                    </div>
                                    <div class="flex justify-between py-3 border-b">
                                        <span class="text-gray-500">Frais</span>
                                        <span class="font-medium">
        @if(Str::contains($payment->service_code, 'om_sn_payout'))
                                                1.4%
                                            @elseif(Str::contains($payment->service_code, 'free_money'))
                                                1.5%
                                            @else
                                                1.5%
                                            @endif
    </span>
                                    </div>
                                    <div class="flex justify-between py-3 border-b">
                                        <span class="text-gray-500">Service</span>
                                        <span class="font-medium">
    @if(Str::contains($payment->service_code, 'wave'))
                                                <img src="{{ asset('images/wave.png') }}" alt="Wave" class="w-5 h-5 inline"> Wave
                                            @elseif(Str::contains($payment->service_code, 'om'))
                                                <img src="{{ asset('images/orange-money.png') }}" alt="Orange Money" class="w-5 h-5 inline"> Orange Money
                                            @elseif(Str::contains($payment->service_code, 'free'))
                                                <img src="{{ asset('images/free-money.png') }}" alt="Free Money" class="w-5 h-5 inline"> Free Money
                                            @endif
</span>
                                    </div>
                                    <div class="flex justify-between py-3 border-b">
                                        <span class="text-gray-500">Date et heure</span>
                                        <span class="font-medium">{{ $payment->created_at->format('d F Y') }} {{ $payment->created_at->format('h:i A') }}</span>
                                    </div>
                                    <div class="flex justify-between py-3 border-b">
                                        <span class="text-gray-500">Nouveau solde</span>
                                        <span class="font-medium" style="color: #059669">{{ number_format($balance, 0) }} FCFA</span>
                                    </div>
                                    <div class="flex justify-between py-3 border-b">
                                        <span class="text-gray-500">ID de transaction</span>
                                        <span class="font-medium text-xs">{{ $payment->external_id }}</span>
                                    </div>
                                </div>

                                <button onclick="closeModal('payment-{{ $payment->id }}')"
                                        class="w-full mt-8 py-3 rounded-lg font-bold text-white"
                                        style="background-color: #059669">
                                    Fermer
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p>Aucun montant reçu pour le moment</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

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
