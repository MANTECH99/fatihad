@extends('merchant.layouts.app')

@section('title', 'Gestion des stocks - ' . $shop->name)
@section('header', 'Gestion des stocks')

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
                    Passez à un plan <strong>payant</strong> pour débloquer la gestion des stocks.
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
    <div class="{{ $userPlan === 'free' ? 'blur-sm pointer-events-none select-none' : '' }}">
        {{-- Barre supérieure : État des stocks et Actions --}}
        <div class="bg-white p-4 rounded-lg border border-gray-200 mb-6 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <i class="fas fa-boxes text-amber-500 text-xl"></i>
                <span class="font-medium text-gray-700">État des stocks</span>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ $products->count() }}</span>
            </div>
            <div class="flex items-center space-x-6 text-sm">
                {{-- Lien Nouvel article redirige vers la création produit --}}
                <a href="{{ route('merchant.products.create', $shop) }}" class="text-purple-600 hover:text-purple-800 flex items-center">
                    <i class="fas fa-plus-circle mr-2"></i> Nouvel article
                </a>
                {{-- Lien Mouvement (à implémenter plus tard) --}}
                <button @click="$store.stockManager.openMovementModal = true" class="text-blue-600 hover:text-blue-800 flex items-center cursor-pointer">
                    <i class="fas fa-exchange-alt mr-2"></i> Ajouter mouvement
                </button>
            </div>

            {{-- Onglets --}}
            <div class="flex space-x-4 border-b border-gray-200 mb-6">
                <button @click="$store.stockManager.activeTab = 'catalogue'"
                        :class="$store.stockManager.activeTab === 'catalogue' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                        class="pb-2 font-medium">
                    <span class="lg:hidden">Catalogue des articles</span>
                    <span class="hidden lg:inline">Catalogue des articles</span>
                </button>
                <button @click="$store.stockManager.activeTab = 'history'"
                        :class="$store.stockManager.activeTab === 'history' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                        class="pb-2 font-medium">
                    <span class="lg:hidden">Nos mouvements</span>
                    <span class="hidden lg:inline">Historique des mouvements</span>
                </button>
            </div>
        </div>

        {{-- Statistiques (Valeur, Bénéfice latent, Alertes) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-6 mb-8">
            @php
                $totalCost = 0;
                $totalSell = 0;
                $alertProducts = $products->filter(function($p) {
                    return $p->track_inventory && $p->stock <= $p->stock_alert;
                });

                foreach($products as $p) {
                    if($p->stock > 0) {
                        $totalCost += ($p->cost_price ?? 0) * $p->stock;
                        $sellPrice = $p->sale_price ?? $p->price;
                        $totalSell += $sellPrice * $p->stock;
                    }
                }
                $beneficeLatent = $totalSell - $totalCost;
            @endphp

            {{-- Valeur du stock (Achat) --}}
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-5 lg:p-6 flex items-center justify-between shadow-sm lg:shadow-none">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur du stock (Achat)</p>
                    <p class="mt-2 text-xl lg:text-3xl font-bold text-gray-900">{{ number_format($totalCost, 0, ',', ' ') }} <span class="text-sm font-normal text-gray-500">FCFA</span></p>
                </div>
                <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-shopping-basket text-blue-600 text-xl lg:text-2xl"></i>
                </div>
            </div>

            {{-- Bénéfice latent --}}
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-5 lg:p-6 flex items-center justify-between shadow-sm lg:shadow-none">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Bénéfice latent</p>
                    <p class="mt-2 text-xl lg:text-3xl font-bold text-green-600">+{{ number_format($beneficeLatent, 0, ',', ' ') }} <span class="text-sm font-normal text-gray-500">FCFA</span></p>
                </div>
                <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-chart-pie text-green-600 text-xl lg:text-2xl"></i>
                </div>
            </div>

            {{-- Alertes rupture --}}
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-5 lg:p-6 flex items-center justify-between shadow-sm lg:shadow-none">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Alertes rupture</p>
                    <p class="mt-2 text-xl lg:text-3xl font-bold {{ $alertProducts->count() > 0 ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $alertProducts->count() }} <span class="text-sm font-normal text-gray-500">article(s) concerné(s)</span>
                    </p>
                </div>
                <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full {{ $alertProducts->count() > 0 ? 'bg-red-100' : 'bg-gray-100' }} flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle {{ $alertProducts->count() > 0 ? 'text-red-600' : 'text-gray-400' }} text-xl lg:text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Catalogue des articles (Liste) --}}
        <div x-show="$store.stockManager.activeTab === 'catalogue'">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <div class="flex items-center">
                    <i class="fas fa-book-open text-amber-400 mr-3"></i>
                    <span class="font-medium text-gray-700">
    <span class="lg:hidden">Catalogue</span>
    <span class="hidden lg:inline">Catalogue des articles</span>
</span>
                    <span class="ml-2 bg-gray-200 text-gray-700 text-xs px-2 py-0.5 rounded">{{ $products->count() }}</span>
                </div>
                <span class="text-sm text-gray-500">{{ $products->sum('stock') }} en stock total</span>
            </div>

            @if($products->isNotEmpty())
                <!-- Version mobile : cartes -->
                <div class="md:hidden divide-y divide-gray-100">
                    @foreach($products as $product)
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                    @if($product->supplier)
                                        <p class="text-xs text-gray-400">Fournisseur: {{ $product->supplier }}</p>
                                    @endif
                                </div>
                                <div>
                                    @if($product->is_available && $product->track_inventory && $product->stock <= $product->stock_alert)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Alerte
                                    </span>
                                    @elseif($product->is_available && (!$product->track_inventory || $product->stock > 0))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <i class="fas fa-check-square mr-1"></i> En stock
                                    </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                        <i class="fas fa-ban mr-1"></i> Inactif
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-sm mt-2">
                                <div>
                                    <p class="text-xs text-gray-500">Quantité</p>
                                    <p class="font-medium">{{ $product->track_inventory ? $product->stock : '∞' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Statut</p>
                                    <p class="font-medium">
                                        @if($product->is_available && $product->track_inventory && $product->stock <= $product->stock_alert)
                                            <span class="text-red-600">Alerte stock</span>
                                        @elseif($product->is_available && (!$product->track_inventory || $product->stock > 0))
                                            <span class="text-green-600">Disponible</span>
                                        @else
                                            <span class="text-gray-600">Inactif</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Prix d'achat</p>
                                    <p>{{ $product->cost_price ? number_format($product->cost_price, 0, ',', ' ') . ' FCFA' : '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Prix de vente</p>
                                    <p class="font-medium">{{ number_format($product->current_price, 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Marge unitaire</p>
                                    <p class="font-medium text-green-600">
                                        @if($product->cost_price)
                                            +{{ number_format($product->current_price - $product->cost_price, 0, ',', ' ') }} FCFA
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Version desktop : tableau -->
                <div class="hidden md:block">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-400 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 font-medium">Désignation</th>
                        <th class="px-6 py-4 font-medium text-center">Qté</th>
                        <th class="px-6 py-4 font-medium text-right">Prix Achat</th>
                        <th class="px-6 py-4 font-medium text-right">Prix Vente</th>
                        <th class="px-6 py-4 font-medium text-right">Marge (unitaire)</th>
                        <th class="px-6 py-4 font-medium text-center">Statut</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @foreach($products as $product)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $product->name }}
                                @if($product->supplier)
                                    <p class="text-xs text-gray-400 font-normal mt-0.5">Fournisseur: {{ $product->supplier }}</p>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center font-medium">
                                {{ $product->track_inventory ? $product->stock : '∞' }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                {{ $product->cost_price ? number_format($product->cost_price, 0, ',', ' ') : '-' }}
                            </td>

                            <td class="px-6 py-4 text-right font-medium">
                                {{ number_format($product->current_price, 0, ',', ' ') }}
                            </td>

                            <td class="px-6 py-4 text-right font-medium text-green-600">
                                @if($product->cost_price)
                                    +{{ number_format($product->current_price - $product->cost_price, 0, ',', ' ') }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($product->is_available && $product->track_inventory && $product->stock <= $product->stock_alert)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Alerte
                                    </span>
                                @elseif($product->is_available && (!$product->track_inventory || $product->stock > 0))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <i class="fas fa-check-square mr-1"></i> En stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                        <i class="fas fa-ban mr-1"></i> Inactif
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-box-open text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Aucun article dans le catalogue.</p>
                </div>
            @endif
        </div>

    </div>



        {{-- Historique des mouvements --}}
        <div x-show="$store.stockManager.activeTab === 'history'">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-medium text-gray-700">Mouvements récents</h3>
                </div>

                @if($movements->isNotEmpty())
                    <!-- Version mobile : cartes -->
                    <div class="md:hidden divide-y divide-gray-100">
                        @foreach($movements as $movement)
                            <div class="p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $movement->product->name ?? 'Produit supprimé' }}</p>
                                        <p class="text-xs text-gray-500">{{ $movement->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div>
                                        @if($movement->type === 'entry')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        📦 Entrée
                                    </span>
                                        @elseif($movement->type === 'return')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        ↩️ Retour
                                    </span>
                                        @elseif($movement->type === 'loss')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        ⚠️ Perte
                                    </span>
                                        @elseif($movement->type === 'sortie')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        📤 Sortie
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3 text-sm mt-2">
                                    <div>
                                        <p class="text-xs text-gray-500">Quantité</p>
                                        <p class="font-medium">{{ $movement->quantity }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Motif</p>
                                        <p>{{ $movement->reason ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Nvx prix d'achat</p>
                                        <p>{{ $movement->new_cost_price ? number_format($movement->new_cost_price, 0, ',', ' ') . ' FCFA' : '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Nvx prix de vente</p>
                                        <p>{{ $movement->new_sale_price ? number_format($movement->new_sale_price, 0, ',', ' ') . ' FCFA' : '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Version desktop : tableau -->
                    <div class="hidden md:block">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-400 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 font-medium">Date</th>
                        <th class="px-6 py-4 font-medium">Produit</th>
                        <th class="px-6 py-4 font-medium text-center">Type</th>
                        <th class="px-6 py-4 font-medium text-center">Quantité</th>
                        <th class="px-6 py-4 font-medium">Motif</th>
                        <th class="px-6 py-4 font-medium text-right">Nvx Prix Achat</th>
                        <th class="px-6 py-4 font-medium text-right">Nvx Prix Vente</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($movements as $movement)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-gray-500">
                                {{ $movement->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $movement->product->name ?? 'Produit supprimé' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($movement->type === 'entry')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                📦 Entrée
                            </span>
                                @elseif($movement->type === 'return')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                ↩️ Retour
                            </span>
                                @elseif($movement->type === 'loss')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                ⚠️ Perte
                            </span>
                                @elseif($movement->type === 'sortie')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
        📤 Sortie
    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center font-medium">
                                {{ $movement->quantity }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $movement->reason ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                {{ $movement->new_cost_price ? number_format($movement->new_cost_price, 0, ',', ' ') . ' FCFA' : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                {{ $movement->new_sale_price ? number_format($movement->new_sale_price, 0, ',', ' ') . ' FCFA' : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                Aucun mouvement de stock enregistré pour le moment.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">Aucun mouvement de stock enregistré pour le moment.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Mouvement de stock --}}
    <div x-show="$store.stockManager.openMovementModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
        <div @click.away="$store.stockManager.openMovementModal = false" class="bg-white rounded-lg shadow-xl w-full max-w-3xl overflow-hidden">
            {{-- En-tête du modal --}}
            <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exchange-alt text-blue-500 mr-3"></i>
                    <h3 class="text-lg font-medium text-gray-900">Mouvement de stock</h3>
                </div>
                <button @click="$store.stockManager.openMovementModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('merchant.products.stock-movement', $shop) }}" method="POST" class="p-6">
                @csrf

                {{-- Produit --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Produit <span class="text-red-500">*</span></label>
                    <select name="product_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">— Sélectionner —</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->stock }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    {{-- Type --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                        <select name="type" id="movementType" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="entry">📦 Entrée</option>
                            <option value="return">↩️ Retour</option>
                            <option value="loss">⚠️ Perte</option>
                        </select>
                    </div>

                    {{-- Quantité --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantité <span class="text-red-500">*</span></label>
                        <input type="number" name="quantity" value="1" min="1" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- Motif --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motif</label>
                    <input type="text" name="reason" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Ex: Réapprovisionnement, casse, retour client...">
                </div>

                {{-- Nouveaux prix (optionnels) --}}
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nouveau prix d'achat <span class="text-xs text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <input type="number" name="new_cost_price" min="0" step="100" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Inchangé si vide">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nouveau prix de vente <span class="text-xs text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <input type="number" name="new_sale_price" min="0" step="100" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Inchangé si vide">
                    </div>
                </div>

                {{-- Bouton valider --}}
                <div class="flex justify-end border-t pt-4">
                    <button type="submit" class="px-6 py-2 bg-blue-400 hover:bg-blue-500 text-white rounded-md shadow-sm transition">
                        <i class="fas fa-save mr-2"></i> Appliquer le mouvement
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('alpine:init', function() {
            Alpine.store('stockManager', {
                openMovementModal: false,
                activeTab: 'catalogue', // <-- AJOUTEZ CECI
            });
        });
    </script>
@endpush
