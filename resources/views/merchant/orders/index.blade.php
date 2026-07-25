{{-- resources/views/merchant/orders/index.blade.php --}}
@extends('merchant.layouts.app')

@section('title', 'Commandes - ' . $shop->name)
@section('header', 'Commandes - ' . $shop->name)

@section('content')
    <div x-data="orderManager()">
        <!-- Stats rapides -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
            <div class="bg-white rounded-lg shadow p-3 text-center">
                <p class="text-xs text-gray-500">Total</p>
                <p class="text-xl font-bold">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-yellow-50 rounded-lg shadow p-3 text-center">
                <p class="text-xs text-yellow-600">En attente</p>
                <p class="text-xl font-bold text-yellow-700">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-blue-50 rounded-lg shadow p-3 text-center">
                <p class="text-xs text-blue-600">En cours</p>
                <p class="text-xl font-bold text-blue-700">{{ $stats['processing'] }}</p>
            </div>
            <div class="bg-green-50 rounded-lg shadow p-3 text-center">
                <p class="text-xs text-green-600">Livrées</p>
                <p class="text-xl font-bold text-green-700">{{ $stats['delivered'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-3 text-center">
                <p class="text-xs text-gray-500">Aujourd'hui</p>
                <p class="text-xl font-bold">{{ number_format($stats['revenue_today'], 0, ',', ' ') }}</p>
            </div>
        </div>





        <!-- Stats paiements -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            @php
                $paidCount = $shop->orders()->where('payment_status', 'paid')->count();
                $paidAmount = $shop->orders()->where('payment_status', 'paid')->sum('total');
                $pendingPayment = $shop->orders()->where('payment_status', 'pending')->count();
                $failedPayment = $shop->orders()->where('payment_status', 'failed')->count();
            @endphp
            <div class="bg-green-50 rounded-lg shadow p-3 text-center">
                <p class="text-xs text-green-600">💳 Payées</p>
                <p class="text-xl font-bold text-green-700">{{ $paidCount }}</p>
                <p class="text-xs text-green-600">{{ number_format($paidAmount, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="bg-yellow-50 rounded-lg shadow p-3 text-center">
                <p class="text-xs text-yellow-600">⏳ En attente</p>
                <p class="text-xl font-bold text-yellow-700">{{ $pendingPayment }}</p>
            </div>
            <div class="bg-red-50 rounded-lg shadow p-3 text-center">
                <p class="text-xs text-red-600">❌ Échoués</p>
                <p class="text-xl font-bold text-red-700">{{ $failedPayment }}</p>
            </div>
            <div class="bg-blue-50 rounded-lg shadow p-3 text-center">
                <p class="text-xs text-blue-600">📊 Taux réussite</p>
                <p class="text-xl font-bold text-blue-700">
                    {{ $paidCount + $pendingPayment + $failedPayment > 0 ? round(($paidCount / ($paidCount + $pendingPayment + $failedPayment)) * 100) : 0 }}%
                </p>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form action="{{ route('merchant.orders.index', $shop) }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <input type="text" name="search" placeholder="Rechercher..." value="{{ request('search') }}"
                       class="border-gray-300 rounded-md text-sm">
                <select name="status" class="border-gray-300 rounded-md text-sm">
                    <option value="">Tous les statuts</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                    <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>En préparation</option>
                    <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Prête</option>
                    <option value="out_for_delivery" {{ request('status') == 'out_for_delivery' ? 'selected' : '' }}>En livraison</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Livrée</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                </select>

                {{-- ➡️ AJOUTER ICI --}}
                <select name="payment_status" class="border-gray-300 rounded-md text-sm">
                    <option value="">Tous les paiements</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Payée</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Échoué</option>
                    <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Remboursée</option>
                </select>

                <input type="date" name="date_from" value="{{ request('date_from') }}" class="border-gray-300 rounded-md text-sm">
                <div class="flex space-x-2">
                    <button type="submit" class="bg-emerald-500 text-white px-4 py-2 rounded-md text-sm">
                        <i class="fas fa-filter mr-1"></i> Filtrer
                    </button>
                    <a href="{{ route('merchant.orders.export', $shop) }}" class="border px-4 py-2 rounded-md text-sm hover:bg-gray-100">
                        <i class="fas fa-download mr-1"></i> CSV
                    </a>
                </div>
            </form>
        </div>

        <!-- Tableau des commandes -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if($orders->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">N°</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produits</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paiement</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium">{{ $order->order_number }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium">{{ $order->customer_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->customer_phone }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $order->items->sum('quantity') }} article(s)
                                </td>
                                <td class="px-4 py-3 text-sm font-medium">
                                    {{ number_format($order->total, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full bg-{{ $order->getStatusColor() }}-100 text-{{ $order->getStatusColor() }}-800">
                                        {{ $order->getStatusLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $paymentColors = [
                                            'paid' => 'green',
                                            'pending' => 'yellow',
                                            'failed' => 'red',
                                            'refunded' => 'purple',
                                        ];
                                        $paymentLabels = [
                                            'paid' => 'Payée',
                                            'pending' => 'En attente',
                                            'failed' => 'Échoué',
                                            'refunded' => 'Remboursé',
                                        ];
                                        $color = $paymentColors[$order->payment_status] ?? 'gray';
                                        $label = $paymentLabels[$order->payment_status] ?? $order->payment_status;
                                    @endphp
                                    <span class="px-2 py-1 text-xs rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
        {{ $label }}
    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('merchant.orders.show', ['shop' => $shop, 'order' => $order]) }}"
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-eye mr-1"></i> Détails
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-shopping-cart text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Aucune commande pour le moment.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
