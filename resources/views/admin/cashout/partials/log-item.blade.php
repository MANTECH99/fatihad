@php
    $frais = $log->amount * 0.015;
    $total = $isCashin ? $log->amount + $frais : $log->amount;
    $montantRecu = $isCashin ? $log->amount : $log->amount - $frais;
@endphp
<div class="flex justify-between items-center py-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50 px-2 rounded"
     onclick="openModal('log-{{ $log->id }}')">
    <div>
        <p class="font-medium text-sm">{{ $isCashin ? 'À' : 'De' }} &nbsp;{{ \App\Helpers\PhoneHelper::formatLocal($log->phone) }}</p>
        <p class="text-xs text-gray-400">{{ $log->created_at->format('d F Y') }} {{ $log->created_at->format('h:i A') }}</p>
    </div>
    <div class="text-right">
        @if($isCashin)
            <p class="font-bold" style="color: #E81E25">-{{ number_format($total, 0) }} FCFA</p>
        @else
            <p class="font-bold" style="color: #10B981">+{{ number_format($montantRecu, 0) }} FCFA</p>
        @endif
    </div>
</div>

{{-- Modal --}}
<div id="log-{{ $log->id }}" class="hidden fixed inset-0 z-50 bg-white overflow-y-auto md:max-w-3xl md:mx-auto md:relative md:inset-auto">
    <div class="sticky top-0 bg-white border-b z-10">
        <div class="flex items-center p-4">
            <button onclick="closeModal('log-{{ $log->id }}')" class="mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <p class="font-semibold text-lg">{{ $isCashin ? "Détails de l'envoi" : "Détails de la réception" }}</p>
        </div>
    </div>

    <div class="p-6">
        <div class="text-center mb-4">
            @if($isCashin)
                <p class="text-4xl font-black" style="color: #E81E25">-{{ number_format($total, 0) }} FCFA</p>
                <p class="text-gray-500 mt-2">À {{ \App\Helpers\PhoneHelper::formatLocal($log->phone) }}</p>
            @else
                <p class="text-4xl font-black" style="color: #10B981">+{{ number_format($montantRecu, 0) }} FCFA</p>
                <p class="text-gray-500 mt-2">De {{ \App\Helpers\PhoneHelper::formatLocal($log->phone) }}</p>
            @endif
        </div>

        <div class="space-y-4">
            <div class="flex justify-between py-3 border-b">
                @if($isCashin)
                    <span class="text-gray-500">Montant envoyé</span>
                    <span class="font-medium">{{ number_format($log->amount) }} FCFA</span>
                @else
                    <span class="text-gray-500">Montant reçu</span>
                    <span class="font-medium" style="color: #10B981">{{ number_format($montantRecu, 0) }} FCFA</span>
                @endif
            </div>
            <div class="flex justify-between py-3 border-b">
                <span class="text-gray-500">Type</span>
                <span class="font-medium">
                    @if($isCashin)
                        <span class="text-red-600">📤 Envoi</span>
                    @else
                        <span class="text-green-600">📥 Réception</span>
                    @endif
                </span>
            </div>
            <div class="flex justify-between py-3 border-b">
                <span class="text-gray-500">Statut</span>
                <span class="font-medium">
                    @if($log->status === 'success')
                        <span class="text-green-600">✅ Effectué</span>
                    @elseif($log->status === 'initiated')
                        <span class="text-yellow-600">⏳ En cours</span>
                    @else
                        <span class="text-red-600">❌ Échoué</span>
                    @endif
                </span>
            </div>
            <div class="flex justify-between py-3 border-b">
                <span class="text-gray-500">Frais</span>
                <span class="font-medium">{{ number_format($frais, 2) }} FCFA</span>
            </div>
            <div class="flex justify-between py-3 border-b">
                <span class="text-gray-500">Service</span>
                <span class="font-medium">
                    @if(Str::contains($log->service_code, 'WAVE'))
                        <img src="{{ asset('images/wave.png') }}" alt="Wave" class="w-5 h-5 inline">
                    @elseif(Str::contains($log->service_code, 'OM'))
                        <img src="{{ asset('images/orange-money.png') }}" alt="Orange Money" class="w-5 h-5 inline">
                    @elseif(Str::contains($log->service_code, 'FM'))
                        <img src="{{ asset('images/free-money.png') }}" alt="Free Money" class="w-5 h-5 inline">
                    @elseif(Str::contains($log->service_code, 'WIZALL'))
                        <img src="{{ asset('images/wizalls.png') }}" alt="Wizall" class="w-5 h-5 inline">
                    @endif
                </span>
            </div>
            <div class="flex justify-between py-3 border-b">
                <span class="text-gray-500">Date et heure</span>
                <span class="font-medium">{{ $log->created_at->format('d F Y') }} {{ $log->created_at->format('h:i A') }}</span>
            </div>
            <div class="flex justify-between py-3 border-b">
                <span class="text-gray-500">Nouveau solde</span>
                <span class="font-medium" style="color: #4D1111">{{ number_format($balance ?? 0, 0) }} FCFA</span>
            </div>
            <div class="flex justify-between py-3 border-b">
                <span class="text-gray-500">ID de transaction</span>
                <span class="font-medium text-xs">{{ $log->external_id }}</span>
            </div>
        </div>

        <button onclick="closeModal('log-{{ $log->id }}')"
                class="w-full mt-8 py-3 rounded-lg font-bold text-white"
                style="background-color: #4D1111">
            Fermer
        </button>
    </div>
</div>
