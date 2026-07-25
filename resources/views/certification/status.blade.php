@extends('merchant.layouts.app')

@section('title', 'Ma Certification')

@section('content')
    <div class="max-w-4xl mx-auto mt-10 px-4">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">

            <!-- Header -->
            <div class="flex items-center justify-between mb-6 border-b pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Ma Certification</h2>
                        <p class="text-xs text-gray-400">Statut de votre badge de confiance</p>
                    </div>
                </div>
                <a href="{{ route('merchant.dashboard') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>

            @if($activeCerts->isNotEmpty())
                @foreach($activeCerts as $activeCert)
                {{-- Certification Active --}}
                <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-8 text-center">
                    <div class="flex justify-center mb-4">
                        <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-4xl">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                    </div>

                    <h3 class="text-2xl font-bold text-green-800 mb-1">
                        {{ \App\Services\PlanService::$certifications[$activeCert->plan]['name'] }}
                    </h3>

                    <div class="inline-flex items-center gap-2 bg-green-100 text-green-700 px-4 py-1 rounded-full text-sm font-medium mb-3">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        Actif
                    </div>

                    <p class="text-gray-600 text-sm mb-2">
                        <i class="fas fa-store text-gray-400 mr-1"></i>
                        Boutique certifiée : <span class="font-semibold">{{ $activeCert->shop->name ?? $activeCert->entity_name }}</span>
                    </p>

                    <p class="text-gray-500 text-sm">
                        <i class="far fa-calendar-alt text-gray-400 mr-1"></i>
                        Expire le : <span class="font-medium">{{ $activeCert->expires_at->format('d/m/Y') }}</span>
                        @if($activeCert->expires_at->isPast())
                            <span class="text-red-500 ml-2">(Expiré)</span>
                        @elseif($activeCert->expires_at->diffInDays(now()) < 30)
                            <span class="text-orange-500 ml-2">(Renouvellement bientôt)</span>
                        @endif
                    </p>

                    {{-- ✅ AFFICHAGE DU PRIX MENSUEL --}}
                    <p class="text-sm text-indigo-600 mt-1">
                        <i class="fas fa-tag mr-1"></i>
                        {{ number_format(\App\Services\PlanService::$certifications[$activeCert->plan]['price'], 0, ',', ' ') }} FCFA / an
                    </p>

                    <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('certification.index') }}" class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition">
                            <i class="fas fa-sync-alt mr-2"></i> Renouveler / Changer
                        </a>
                    </div>
                </div>
                @endforeach
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h4 class="font-medium text-gray-700 mb-2">Avantages de votre certification :</h4>
                    <ul class="space-y-1 text-sm text-gray-600">
                        @foreach(\App\Services\PlanService::$certifications[$activeCert->plan]['features'] as $feature)
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-500"></i> {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                </div>

            @else
                {{-- Aucune certification active --}}
                <div class="text-center py-12">
                    <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 text-4xl mx-auto mb-4">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Vous n'avez pas de certification</h3>
                    <p class="text-gray-500 text-sm mb-6 max-w-sm mx-auto">
                        Obtenez un badge de confiance pour augmenter vos ventes et la crédibilité de vos boutiques.
                    </p>
                    <a href="{{ route('certification.index') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-xl font-medium hover:bg-emerald-700 transition shadow-sm">
                        <i class="fas fa-plus-circle mr-2"></i> Obtenir une certification
                    </a>
                </div>
            @endif

            {{-- Historique (optionnel) --}}
            @if(isset($history) && $history->count() > 0)
                <div class="mt-8 border-t pt-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Historique</h4>
                    <div class="space-y-2">
                        @foreach($history as $cert)
                            <div class="flex items-center justify-between text-sm border-b border-gray-100 pb-2">
                                <div>
                                    <span class="font-medium text-gray-700">{{ \App\Services\PlanService::$certifications[$cert->plan]['name'] }}</span>
                                    <span class="text-gray-400 text-xs ml-2">({{ $cert->entity_name }})</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-400">{{ $cert->created_at->format('d/m/Y') }}</span>
                                    @if($cert->status === 'active')
                                        <span class="text-green-600 text-xs font-medium bg-green-50 px-2 py-0.5 rounded">Actif</span>
                                    @else
                                        <span class="text-gray-400 text-xs">{{ ucfirst($cert->status) }}</span>
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
