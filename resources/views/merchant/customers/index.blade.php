@extends('merchant.layouts.app')

@section('title', 'Clients - ' . $shop->name)
@section('header', 'Clients - ' . $shop->name)

@section('content')

    @php
        $userPlan = auth()->user()->plan ?? 'free';
    @endphp

    @if($userPlan === 'free')
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.4); backdrop-filter: blur(4px);">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 p-8 max-w-md w-full text-center relative z-50">

                {{-- ❌ Bouton X pour fermer --}}
                <button onclick="this.closest('.fixed').remove(); document.querySelector('[x-data]').classList.remove('blur-sm', 'pointer-events-none', 'select-none')"
                        class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times"></i>
                </button>

                <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4 text-amber-600 text-4xl">
                    <i class="fas fa-crown"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Fonctionnalité Premium</h2>
                <p class="text-gray-500 mb-4">
                    Passez à un plan <strong>payant</strong> pour débloquer Suivi des paiements.
                </p>

                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <p class="text-sm text-gray-500">Votre plan actuel</p>
                    @php
                        $planKey = auth()->user()->plan ?? 'free';
                        $planName = \App\Services\PlanService::$plans[$planKey]['name'] ?? 'Gratuit';
                    @endphp
                    <p class="text-xl font-bold text-gray-800 uppercase">
                        {{ $planName }}
                    </p>
                </div>

                <a href="{{ route('subscription.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 text-white rounded-xl font-medium hover:bg-emerald-600 transition shadow-sm">
                    <i class="fas fa-rocket"></i>
                    Passer au plan Professionnel
                </a>
            </div>
        </div>
    @endif
    <div class="space-y-6 {{ $userPlan === 'free' ? 'blur-sm pointer-events-none select-none' : '' }}">

        <!-- Header mobile uniquement (sans sélecteur) -->
        <div class=" bg-white rounded-xl shadow-sm p-5 mb-6 -mt-4">
            <h1 class="text-2xl font-bold text-gray-900">Clients & CRM</h1>
            <p class="text-sm text-gray-500 mt-1">Gérez votre relation client. Suivez vos meilleurs acheteurs, leur historique d'achats, et contactez-les facilement via WhatsApp.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex flex-col justify-between shadow-sm lg:shadow-none">
                <p class="text-sm text-gray-500">Total clients</p>
                <p class="mt-1 text-base lg:text-2xl font-bold">{{ $stats['total_customers'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex flex-col justify-between shadow-sm lg:shadow-none">
                <p class="text-sm text-gray-500">Nouveaux ce mois</p>
                <p class="mt-1 text-base lg:text-2xl font-bold text-green-600">{{ $stats['new_this_month'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex flex-col justify-between shadow-sm lg:shadow-none">
                <p class="text-sm text-gray-500">Panier moyen</p>
                <p class="mt-1 text-base lg:text-2xl font-bold">{{ number_format($stats['avg_basket'] ?? 0, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex flex-col justify-between shadow-sm lg:shadow-none">
                <p class="text-sm text-gray-500">Taux de rétention</p>
                <p class="mt-1 text-base lg:text-2xl font-bold text-purple-600">{{ $stats['retention_rate'] }}%</p>
            </div>
        </div>
        {{-- Filtres --}}
        <div class="flex flex-wrap gap-2 mb-4">
            <!-- Mobile : select déroulant -->
            <div class="w-full md:hidden">
                <select onchange="window.location.href = this.value" class="w-full border-gray-300 rounded-md text-sm py-2 px-3">
                    <option value="{{ route('merchant.customers.index', $shop) }}" {{ !request('filter') ? 'selected' : '' }}>Tous</option>
                    <option value="{{ route('merchant.customers.index', ['shop' => $shop, 'filter' => 'top']) }}" {{ request('filter') === 'top' ? 'selected' : '' }}>⭐ Meilleurs clients</option>
                    <option value="{{ route('merchant.customers.index', ['shop' => $shop, 'filter' => 'big_spenders']) }}" {{ request('filter') === 'big_spenders' ? 'selected' : '' }}>💰 Plus gros achats</option>
                    <option value="{{ route('merchant.customers.index', ['shop' => $shop, 'filter' => 'inactive']) }}" {{ request('filter') === 'inactive' ? 'selected' : '' }}>💤 Inactifs (3+ mois)</option>
                    <option value="{{ route('merchant.customers.index', ['shop' => $shop, 'filter' => 'recent']) }}" {{ request('filter') === 'recent' ? 'selected' : '' }}>🕒 Récents</option>
                    <option value="{{ route('merchant.customers.index', ['shop' => $shop, 'filter' => 'high_value']) }}" {{ request('filter') === 'high_value' ? 'selected' : '' }}>💎 Panier élevé</option>
                    <option value="{{ route('merchant.customers.index', ['shop' => $shop, 'filter' => 'frequent']) }}" {{ request('filter') === 'frequent' ? 'selected' : '' }}>🔄 Réguliers (5+)</option>
                    <option value="{{ route('merchant.customers.index', ['shop' => $shop, 'filter' => 'new']) }}" {{ request('filter') === 'new' ? 'selected' : '' }}>🆕 Nouveaux</option>
                </select>
            </div>

            <!-- Desktop : boutons -->
            <div class="hidden md:flex flex-wrap gap-2">
                <a href="{{ route('merchant.customers.index', $shop) }}" class="px-4 py-2 rounded-full text-sm {{ !request('filter') ? 'bg-emerald-500 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">Tous</a>
                <a href="{{ route('merchant.customers.index', ['shop' => $shop, 'filter' => 'top']) }}" class="px-4 py-2 rounded-full text-sm {{ request('filter') === 'top' ? 'bg-emerald-500 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">⭐ Meilleurs clients</a>
                <a href="{{ route('merchant.customers.index', ['shop' => $shop, 'filter' => 'big_spenders']) }}" class="px-4 py-2 rounded-full text-sm {{ request('filter') === 'big_spenders' ? 'bg-emerald-500 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">💰 Plus gros achats</a>
                <a href="{{ route('merchant.customers.index', ['shop' => $shop, 'filter' => 'inactive']) }}" class="px-4 py-2 rounded-full text-sm {{ request('filter') === 'inactive' ? 'bg-emerald-500 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">💤 Inactifs (3+ mois)</a>
                <a href="{{ route('merchant.customers.index', ['shop' => $shop, 'filter' => 'recent']) }}" class="px-4 py-2 rounded-full text-sm {{ request('filter') === 'recent' ? 'bg-emerald-500 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">🕒 Récents</a>
                <a href="{{ route('merchant.customers.index', ['shop' => $shop, 'filter' => 'high_value']) }}" class="px-4 py-2 rounded-full text-sm {{ request('filter') === 'high_value' ? 'bg-emerald-500 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">💎 Panier élevé</a>
                <a href="{{ route('merchant.customers.index', ['shop' => $shop, 'filter' => 'frequent']) }}" class="px-4 py-2 rounded-full text-sm {{ request('filter') === 'frequent' ? 'bg-emerald-500 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">🔄 Réguliers (5+)</a>
                <a href="{{ route('merchant.customers.index', ['shop' => $shop, 'filter' => 'new']) }}" class="px-4 py-2 rounded-full text-sm {{ request('filter') === 'new' ? 'bg-emerald-500 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">🆕 Nouveaux</a>
            </div>
        </div>
{{--
        <a href="{{ route('merchant.customers.export', $shop) }}" class="inline-block px-4 py-2 bg-gray-100 rounded-full text-sm hover:bg-gray-200 mb-4">
            📥 Exporter CSV
        </a>
        --}}

        {{-- Tableau --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Version mobile : cartes -->
            <div class="md:hidden divide-y divide-gray-100">
                @forelse($customers as $customer)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <a href="{{ route('merchant.customers.show', ['shop' => $shop, 'customer' => $customer]) }}" class="font-medium text-emerald-600 hover:underline">
                                    {{ $customer->name ?? 'Client ' . $customer->id }}
                                </a>
                                <p class="text-sm text-gray-500">{{ $customer->phone }}</p>
                            </div>
                            <div>
                                @if($customer->tag === 'VIP')
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">⭐ VIP</span>
                                @elseif($customer->tag === 'Régulier')
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">🔄 Régulier</span>
                                @elseif($customer->tag === 'Nouveau')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">🆕 Nouveau</span>
                                @endif
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-sm mt-2">
                            <div>
                                <p class="text-xs text-gray-500">Commandes</p>
                                <p class="font-bold">{{ $customer->total_orders }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Total dépensé</p>
                                <p class="font-bold">{{ number_format($customer->total_spent, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Dernière commande</p>
                                <p>{{ $customer->last_order_at?->diffForHumans() }}</p>
                            </div>
                            <div class="text-right">
                                @if($customer->phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone) }}" target="_blank" class="text-green-600 hover:text-green-800 text-sm">
                                        <i class="fab fa-whatsapp"></i> Contacter
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">Aucun client trouvé.</div>
                @endforelse
            </div>

            <!-- Version desktop : tableau -->
            <div class="hidden md:block">
                <table class="w-full">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tag</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commandes</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total dépensé</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dernière commande</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">
                                <a href="{{ route('merchant.customers.show', ['shop' => $shop, 'customer' => $customer]) }}" class="text-emerald-600 hover:underline">
                                    {{ $customer->name ?? 'Client ' . $customer->id }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $customer->phone }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if($customer->tag === 'VIP')
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">⭐ VIP</span>
                                @elseif($customer->tag === 'Régulier')
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">🔄 Régulier</span>
                                @elseif($customer->tag === 'Nouveau')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">🆕 Nouveau</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm font-bold">{{ $customer->total_orders }}</td>
                            <td class="px-4 py-3 text-sm font-bold">{{ number_format($customer->total_spent, 0, ',', ' ') }} FCFA</td>
                            <td class="px-4 py-3 text-sm">{{ $customer->last_order_at?->diffForHumans() }}</td>
                            <td class="px-4 py-3 text-right">
                                @if($customer->phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone) }}" target="_blank" class="text-green-600 hover:text-green-800">
                                        <i class="fab fa-whatsapp"></i> Contacter
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-8 text-gray-500">Aucun client trouvé.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="px-4 py-3">{{ $customers->links() }}</div>
            </div>
        </div>
    </div>
@endsection
