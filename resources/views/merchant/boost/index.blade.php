{{-- resources/views/merchant/boost/index.blade.php --}}
@extends('merchant.layouts.app')

@section('title', 'Mes campagnes')
@section('header', '📊 Mes campagnes publicitaires')

@section('content')

    @php
        $userPlan = auth()->user()->plan ?? 'free';
    @endphp

    @if($userPlan === 'free')
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.4); backdrop-filter: blur(4px);">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 p-6 md:p-8 max-w-md w-full text-center relative z-50 mx-4">
                <button onclick="this.closest('.fixed').remove(); document.querySelector('[x-data]').classList.remove('blur-sm', 'pointer-events-none', 'select-none')"
                        class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times"></i>
                </button>
                <div class="w-16 md:w-20 h-16 md:h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4 text-amber-600 text-3xl md:text-4xl">
                    <i class="fas fa-crown"></i>
                </div>
                <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-2">Fonctionnalité Premium</h2>
                <p class="text-sm md:text-base text-gray-500 mb-4">
                    Passez à un plan <strong>payant</strong> pour débloquer la gestion des stocks.
                </p>
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <p class="text-sm text-gray-500">Votre plan actuel</p>
                    @php
                        $planKey = auth()->user()->plan ?? 'free';
                        $planName = \App\Services\PlanService::$plans[$planKey]['name'] ?? 'Gratuit';
                    @endphp
                    <p class="text-lg md:text-xl font-bold text-gray-800 uppercase">{{ $planName }}</p>
                </div>
                <a href="{{ route('subscription.index') }}"
                   class="inline-flex items-center gap-2 px-5 md:px-6 py-2.5 md:py-3 bg-emerald-500 text-white rounded-xl font-medium hover:bg-emerald-600 transition shadow-sm text-sm md:text-base">
                    <i class="fas fa-rocket"></i>
                    Passer au plan Professionnel
                </a>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm md:text-base">
            🚀 Campagne lancée avec succès !
            <p class="text-xs md:text-sm mt-1">
                Le paiement sera effectué automatiquement par Facebook selon le moyen de paiement
                enregistré dans votre compte publicitaire.
            </p>
        </div>
    @endif

    {{-- En-tête responsive --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4 {{ $userPlan === 'free' ? 'blur-sm pointer-events-none select-none' : '' }}">
        <p class="text-sm text-gray-500">Gérez vos campagnes publicitaires</p>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('merchant.boost.whatsapp.create', $shop) }}"
               class="px-4 py-2 bg-green-500 text-white rounded-md text-sm hover:bg-green-600">
                📱 Campagne WhatsApp
            </a>
            <a href="{{ route('merchant.products.index', $shop) }}"
               class="px-3 md:px-4 py-2 bg-emerald-500 text-white rounded-md text-xs md:text-sm hover:bg-emerald-600 whitespace-nowrap">
                🚀 Nouveau boost
            </a>
            <a href="{{ route('merchant.boost.retargeting.create', $shop) }}"
               class="px-3 md:px-4 py-2 bg-purple-500 text-white rounded-md text-xs md:text-sm hover:bg-purple-600 whitespace-nowrap">
                🎯 Retargeting
            </a>
            <a href="{{ route('merchant.boost.promote', $shop) }}"
               class="px-3 md:px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-md text-xs md:text-sm hover:from-purple-600 hover:to-pink-600 whitespace-nowrap">
                📢 Promouvoir
            </a>
        </div>
    </div>

    <div class="space-y-4 {{ $userPlan === 'free' ? 'blur-sm pointer-events-none select-none' : '' }}">
        @forelse($campaigns as $campaign)
            <div class="bg-white rounded-lg shadow p-4 md:p-5">
                {{-- Haut de carte responsive --}}
                <div class="flex flex-col sm:flex-row justify-between items-start gap-3">
                    <div class="flex items-center space-x-3 md:space-x-4">
                        @if($campaign->landing_url)
                            @if($campaign->post_image)
                                <img src="{{ asset('storage/' . $campaign->post_image) }}" alt="" class="w-12 h-12 md:w-16 md:h-16 rounded-lg object-cover flex-shrink-0">
                            @else
                                <div class="w-12 h-12 md:w-16 md:h-16 rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-rocket text-white text-lg md:text-2xl"></i>
                                </div>
                            @endif
                            <div>
                                <h4 class="font-bold text-sm md:text-base">{{ $campaign->post_message ? \Str::limit($campaign->post_message, 50) : '📢 Promotion SaaS' }}</h4>
                                <p class="text-xs md:text-sm text-gray-500">🔗 {{ \Str::limit($campaign->landing_url, 30) }}</p>
                            </div>
                        @elseif($campaign->campaign_type === 'retargeting')
                            <div class="w-12 h-12 md:w-16 md:h-16 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-bullseye text-purple-600 text-lg md:text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm md:text-base">🎯 Retargeting automatique</h4>
                                <p class="text-xs md:text-sm text-gray-500">{{ $campaign->name }}</p>
                            </div>

                        @elseif($campaign->campaign_type === 'traffic' && $campaign->whatsapp_number)
                            @if($campaign->whatsapp_image)
                                <img src="{{ asset('storage/' . $campaign->whatsapp_image) }}" alt="" class="w-12 h-12 md:w-16 md:h-16 rounded-lg object-cover flex-shrink-0">
                            @else
                                <div class="w-12 h-12 md:w-16 md:h-16 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fab fa-whatsapp text-green-600 text-lg md:text-2xl"></i>
                                </div>
                            @endif
                            <div>
                                <h4 class="font-bold text-sm md:text-base">📱 Campagne WhatsApp</h4>
                                <p class="text-xs md:text-sm text-gray-500">{{ $campaign->whatsapp_message ? \Str::limit($campaign->whatsapp_message, 50) : $campaign->name }}</p>
                            </div>
                        @else
                            <img src="{{ $campaign->product->image_url }}" alt="" class="w-12 h-12 md:w-16 md:h-16 rounded-lg object-cover">
                            <div>
                                <h4 class="font-bold text-sm md:text-base">{{ $campaign->product->name }}</h4>
                                <p class="text-xs md:text-sm text-gray-500">{{ $campaign->name }}</p>
                            </div>
                        @endif
                        <span class="inline-block px-2 py-1 text-xs rounded-full bg-{{ $campaign->status_color }}-100 text-{{ $campaign->status_color }}-800 whitespace-nowrap">
                            {{ $campaign->status_label }}
                        </span>
                    </div>
                    <div class="text-left sm:text-right w-full sm:w-auto">
                        <p class="text-xs md:text-sm text-gray-500">Budget</p>
                        <p class="font-bold text-sm md:text-base">{{ $campaign->daily_budget }}€/jour</p>
                    </div>
                </div>

                {{-- Statistiques responsive --}}
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3 md:gap-4 mt-4 pt-4 border-t">
                    <div class="text-center">
                        <p class="text-lg md:text-2xl font-bold text-blue-600">{{ number_format($campaign->reach, 0, ',', ' ') }}</p>
                        <p class="text-[10px] md:text-xs text-gray-500">👁️ Personnes touchées</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg md:text-2xl font-bold text-purple-600">{{ number_format($campaign->impressions, 0, ',', ' ') }}</p>
                        <p class="text-[10px] md:text-xs text-gray-500">📺 Impressions</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg md:text-2xl font-bold text-green-600">{{ number_format($campaign->clicks, 0, ',', ' ') }}</p>
                        <p class="text-[10px] md:text-xs text-gray-500">👆 Clics</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg md:text-2xl font-bold text-orange-600">{{ number_format($campaign->spent, 2, ',', ' ') }}€</p>
                        <p class="text-[10px] md:text-xs text-gray-500">💶 Dépensé</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg md:text-2xl font-bold text-red-600">{{ number_format($campaign->ctr, 2, ',', ' ') }}%</p>
                        <p class="text-[10px] md:text-xs text-gray-500">📈 CTR</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg md:text-2xl font-bold text-yellow-600">{{ number_format($campaign->cpc, 2, ',', ' ') }}€</p>
                        <p class="text-[10px] md:text-xs text-gray-500">💸 CPC</p>
                    </div>
                    <div class="text-center col-span-2 md:col-span-1">
                        <p class="text-lg md:text-2xl font-bold text-purple-600">{{ number_format($campaign->cpp, 2, ',', ' ') }}€</p>
                        <p class="text-[10px] md:text-xs text-gray-500">📊 CPM</p>
                    </div>
                </div>

                {{-- Actions responsive --}}
                <div class="flex flex-wrap justify-end gap-2 md:gap-3 mt-4 pt-4 border-t">
                    @if($campaign->status === 'active')
                        <button onclick="pauseCampaign({{ $campaign->id }})" class="text-yellow-600 hover:text-yellow-800 text-xs md:text-sm">
                            ⏸️ Pause
                        </button>
                    @elseif($campaign->status === 'paused')
                        <button onclick="resumeCampaign({{ $campaign->id }})" class="text-green-600 hover:text-green-800 text-xs md:text-sm">
                            ▶️ Relancer
                        </button>
                    @endif
                    <button onclick="syncStats({{ $campaign->id }})" class="text-blue-600 hover:text-blue-800 text-xs md:text-sm">
                        🔄 Actualiser
                    </button>
                    <form action="{{ route('merchant.boost.duplicate', ['shop' => $shop, 'campaign' => $campaign]) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-800 text-xs md:text-sm">
                            📋 Dupliquer
                        </button>
                    </form>
                        @if($campaign->status === 'draft')
                            <a href="{{ route('merchant.boost.edit', ['shop' => $shop, 'campaign' => $campaign]) }}"
                               class="text-blue-600 hover:text-blue-800 text-xs md:text-sm flex items-center">
                                ✏️ Modifier
                            </a>
                            <form action="{{ route('merchant.boost.launch', ['shop' => $shop, 'campaign' => $campaign]) }}" method="POST" class="inline-flex items-center">
                                @csrf
                                <button type="submit" class="text-emerald-600 hover:text-emerald-800 text-xs md:text-sm">
                                    🚀 Lancer
                                </button>
                            </form>
                        @endif
                </div>
            </div>
        @empty
            <div class="text-center py-8 md:py-12 bg-white rounded-lg shadow">
                <p class="text-gray-500 text-sm md:text-base">Aucune campagne pour le moment.</p>
            </div>
        @endforelse

        <div class="text-sm md:text-base">
            {{ $campaigns->links() }}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        async function pauseCampaign(id) {
            const response = await fetch(`/merchant/shops/{{ $shop->id }}/boost/${id}/pause`, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
            });
            if (response.ok) location.reload();
        }

        async function resumeCampaign(id) {
            const response = await fetch(`/merchant/shops/{{ $shop->id }}/boost/${id}/resume`, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
            });
            if (response.ok) location.reload();
        }

        async function syncStats(id) {
            const response = await fetch(`/merchant/shops/{{ $shop->id }}/boost/${id}/sync`, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
            });
            if (response.ok) location.reload();
        }
    </script>

    <script>
        async function launchRetargeting() {
            if (!confirm('Lancer une campagne de retargeting à 5€/jour pendant 7 jours ?')) return;

            const response = await fetch('{{ route("merchant.boost.retargeting", $shop) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    daily_budget: 5,
                    duration_days: 7
                })
            });

            if (response.ok) {
                location.reload();
            } else {
                alert('Erreur lors du lancement');
            }
        }
    </script>
@endpush
