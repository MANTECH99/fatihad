@extends('merchant.layouts.app')

@section('title', 'Mon abonnement')
@section('header', 'Mon abonnement')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold">Plan actuel : <span class="text-emerald-600">{{ \App\Services\PlanService::$plans[$currentPlan]['name'] }}</span></h2>

            {{-- Plan gratuit : période d'essai --}}
            @if($subscription && $subscription->trial_ends_at && $subscription->trial_ends_at->isFuture())
                <p class="text-sm text-gray-500 mt-1">Période d'essai jusqu'au {{ $subscription->trial_ends_at->format('d/m/Y à H:i') }}</p>
            @endif

            {{-- Plan payant : date d'expiration --}}
            @if($subscription && $subscription->ends_at && $currentPlan !== 'free')
                <p class="text-sm text-gray-500 mt-1">Abonnement valide jusqu'au {{ $subscription->ends_at->format('d/m/Y à H:i') }}</p>
            @endif

            {{-- Plan expiré --}}
            @if($subscription && $subscription->ends_at && $subscription->ends_at->isPast() && $currentPlan === 'free')
                <p class="text-sm text-red-500 mt-1">Abonnement expiré</p>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @foreach($plans as $key => $plan)
                <div class="bg-white rounded-2xl shadow-sm border-2 p-6 text-center {{ $currentPlan === $key ? 'border-emerald-500' : 'border-gray-200' }}">
                    @if($key === 'starter')
                        <span class="bg-emerald-500 text-white text-xs font-bold px-3 py-1 rounded-full">Populaire</span>
                    @endif
                    <h3 class="font-bold text-lg mt-2">{{ $plan['name'] }}</h3>
                    <p class="text-3xl font-black mt-3">
                        @if($plan['price'] === 0)
                            Gratuit
                        @else
                            {{ number_format($plan['price'], 0, ',', ' ') }}
                            <span class="text-sm font-normal text-gray-400">FCFA/mois</span>
                        @endif
                    </p>
                    <ul class="text-sm text-gray-600 mt-4 space-y-2 text-left">
                        <li>🛍️ {{ $plan['shops'] === -1 ? 'Illimité' : $plan['shops'] }} boutique(s)</li>
                        <li>📦 {{ $plan['products'] === -1 ? 'Illimité' : $plan['products'] }} produits</li>
                        @if(in_array('payments', $plan['features']))<li>💳 Paiements Wave/OM</li>@endif
                        @if(in_array('advanced_stats', $plan['features']))<li>📊 Stats avancées</li>@endif
                        @if(in_array('reviews', $plan['features']))<li>⭐ Avis clients</li>@endif
                        @if(in_array('priority_support', $plan['features']))<li>🎧 Support prioritaire</li>@endif
                    </ul>

                    @if($currentPlan === $key)
                        <button class="w-full mt-6 py-2.5 bg-gray-100 text-gray-500 rounded-xl font-medium" disabled>Plan actuel</button>
                    @elseif($key === 'free')
                        <form action="{{ route('subscription.subscribe') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan" value="free">
                            <button class="w-full mt-6 py-2.5 bg-emerald-500 text-white rounded-xl font-medium hover:bg-emerald-600">
                                Revenir au gratuit
                            </button>
                        </form>
                    @else
                        <a href="{{ route('subscription.payment', $key) }}"
                           class="block w-full mt-6 py-2.5 bg-emerald-500 text-white rounded-xl font-medium hover:bg-emerald-600 text-center">
                            Choisir
                        </a>
                    @endif
                </div>
            @endforeach
        </div>

        @if($currentPlan !== 'free')
            <div class="text-center mt-8">
                <form action="{{ route('subscription.cancel') }}" method="POST" onsubmit="return confirm('Annuler votre abonnement ?')">
                    @csrf
                    <button class="text-red-500 text-sm hover:underline">Annuler mon abonnement</button>
                </form>
            </div>
        @endif
    </div>
@endsection
