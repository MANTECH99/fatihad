@extends('merchant.layouts.app')

@section('title', 'Ventes - ' . $shop->name)
@section('header', 'Ventes - ' . $shop->name)

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
                    Passez à un plan <strong>payant</strong> pour débloquer la gestion des ventes.
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
                    Passer au plan payant
                </a>
            </div>
        </div>
    @endif
    {{-- <div x-data="saleManager()">--}}
    <div x-data="saleManager()" class="{{ $userPlan === 'free' ? 'blur-sm pointer-events-none select-none' : '' }}">
        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 sm:gap-4 mb-6">
            {{-- Total ventes --}}
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex items-center justify-between shadow-sm lg:shadow-none">
                <div>
                    <p class="text-sm text-gray-500">Total ventes</p>
                    <p class="mt-1 text-sm lg:text-xl font-bold text-gray-900">{{ number_format($totalSales, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-coins text-blue-600 text-base lg:text-xl"></i>
                </div>
            </div>

            {{-- Nb. transactions --}}
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex items-center justify-between shadow-sm lg:shadow-none">
                <div>
                    <p class="text-sm text-gray-500">Transactions</p>
                    <p class="mt-1 text-sm lg:text-xl    font-bold text-gray-900">{{ $totalTransactions }} ventes</p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-gray-600 text-base lg:text-xl"></i>
                </div>
            </div>

            {{-- Ventes en ligne --}}
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex items-center justify-between shadow-sm lg:shadow-none">
                <div>
                    <p class="text-sm text-gray-500"><span class="lg:hidden">V. en ligne</span><span class="hidden lg:inline">Ventes en ligne</span></p>
                    <p class="mt-1 text-sm lg:text-xl font-bold text-blue-600">{{ number_format($totalOnline, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-globe text-blue-600 text-base lg:text-xl"></i>
                </div>
            </div>

            {{-- Ventes physiques --}}
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex items-center justify-between shadow-sm lg:shadow-none">
                <div>
                    <p class="text-sm text-gray-500"><span class="lg:hidden">V. physiques</span><span class="hidden lg:inline">Ventes physiques</span></p>
                    <p class="mt-1 text-sm lg:text-xl font-bold text-emerald-600">{{ number_format($totalPhysical, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-full bg-emerald-100 flex items-center justify-center">
                    <i class="fas fa-store text-emerald-600 text-base lg:text-xl"></i>
                </div>
            </div>

            {{-- Marge net --}}
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex items-center justify-between shadow-sm lg:shadow-none">
                <div>
                    <p class="text-sm text-gray-500">Marge net</p>
                    <p class="mt-1 text-sm lg:text-xl font-bold text-purple-600">{{ number_format($totalProfit, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-full bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600 text-base lg:text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Nouvelle vente --}}
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h2 class="text-lg font-semibold">💳 Nouvelle vente physique</h2>
            </div>
            <div class="p-6">
                <form action="{{ route('merchant.sales.store', $shop) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Produit</label>
                            <select name="product_id" required class="w-full border-gray-300 rounded-md">
                                <option value="">Choisir un produit</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }} - {{ number_format($product->current_price, 0, ',', ' ') }} FCFA
                                        @if($product->track_inventory) (Stock: {{ $product->stock }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Qté</label>
                            <input type="number" name="quantity" min="1" value="1" required class="w-full border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                            <input type="text" name="customer_name" placeholder="Nom" class="w-full border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tél</label>
                            <input type="text" name="customer_phone" placeholder="77 123 45 67" class="w-full border-gray-300 rounded-md">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-emerald-500 text-white px-4 py-2.5 rounded-md hover:bg-emerald-600 whitespace-nowrap w-full sm:w-auto">
                                <i class="fas fa-plus mr-1"></i> Enregistrer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Ventes récentes --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold">Ventes récentes</h2>
            </div>
            <div class="p-6">
                @if($recentSales->isNotEmpty())

                    <!-- Version mobile : cartes -->
                    <div class="md:hidden space-y-3">
                        @foreach($recentSales as $sale)
                            <div class="border border-gray-100 rounded-xl p-4 hover:bg-gray-50 transition">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <p class="font-medium">💵 {{ $sale['name'] }}</p>
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($sale['date'])->format('d/m/Y') }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $sale['type'] === 'physical' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $sale['channel'] }}
                            </span>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-sm mt-2">
                                    <div>
                                        <p class="text-gray-500 text-xs">Client</p>
                                        <p>{{ $sale['customer_name'] ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-xs">Téléphone</p>
                                        <p>{{ $sale['customer_phone'] ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-xs">Prix d'achat</p>
                                        <p>{{ $sale['cost_price'] ? number_format($sale['cost_price'], 0, ',', ' ') . ' FCFA' : '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-xs">Prix de vente</p>
                                        <p class="font-medium">{{ number_format($sale['amount'], 0, ',', ' ') }} FCFA</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-xs">Marge</p>
                                        <p class="font-medium {{ $sale['profit'] > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                            {{ $sale['profit'] > 0 ? number_format($sale['profit'], 0, ',', ' ') . ' FCFA' : '-' }}
                                        </p>
                                    </div>
                                </div>

                                @if($sale['type'] === 'physical')
                                    <div class="mt-3 pt-3 border-t border-gray-100 flex justify-end">
                                        <form action="{{ route('merchant.sales.destroy', ['shop' => $shop, 'sale' => $sale['id']]) }}" method="POST" onsubmit="return confirm('Supprimer cette vente ?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-500 hover:text-red-700 text-sm inline-flex items-center">
                                                <i class="fas fa-trash mr-1"></i> Supprimer
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Version desktop : tableau -->
                    <div class="hidden md:block">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Canal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix d'achat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix de vente</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marge</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y">
                        @foreach($recentSales as $sale)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">💵 {{ $sale['name'] }}</td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $sale['customer_name'] ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $sale['customer_phone'] ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($sale['date'])->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                <span class="px-2 py-1 text-xs rounded-full {{ $sale['type'] === 'physical' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                    {{ $sale['channel'] }}
                </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $sale['cost_price'] ? number_format($sale['cost_price'], 0, ',', ' ') . ' FCFA' : '-' }}
                                </td>
                                <td class="px-4 py-3 font-medium">{{ number_format($sale['amount'], 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3 font-medium {{ $sale['profit'] > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $sale['profit'] > 0 ? number_format($sale['profit'], 0, ',', ' ') . ' FCFA' : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($sale['type'] === 'physical')
                                        <form action="{{ route('merchant.sales.destroy', ['shop' => $shop, 'sale' => $sale['id']]) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette vente ?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-500 hover:text-red-700">🗑️</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Aucune vente enregistrée.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
