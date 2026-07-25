{{-- resources/views/admin/orders/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Commandes')
@section('header', 'Gestion des commandes')

@section('content')
    <div x-data="adminOrderManager()">
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-sm text-gray-500">Total</p>
                <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-yellow-50 rounded-lg shadow p-4 text-center">
                <p class="text-sm text-yellow-600">En attente</p>
                <p class="text-2xl font-bold text-yellow-700">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-blue-50 rounded-lg shadow p-4 text-center">
                <p class="text-sm text-blue-600">En cours</p>
                <p class="text-2xl font-bold text-blue-700">{{ $stats['processing'] }}</p>
            </div>
            <div class="bg-green-50 rounded-lg shadow p-4 text-center">
                <p class="text-sm text-green-600">Livrées</p>
                <p class="text-2xl font-bold text-green-700">{{ $stats['delivered'] }}</p>
            </div>
            <div class="bg-red-50 rounded-lg shadow p-4 text-center">
                <p class="text-sm text-red-600">Annulées</p>
                <p class="text-2xl font-bold text-red-700">{{ $stats['cancelled'] }}</p>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <input type="text" name="search" placeholder="N° commande, client..." value="{{ request('search') }}"
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
                <select name="payment_status" class="border-gray-300 rounded-md text-sm">
                    <option value="">Paiement</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Payé</option>
                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Échoué</option>
                </select>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="border-gray-300 rounded-md text-sm" placeholder="Du">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm">
                    <i class="fas fa-filter mr-1"></i> Filtrer
                </button>
            </form>
        </div>

        <!-- Tableau -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">N°</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Boutique</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paiement</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-indigo-600 hover:text-indigo-800 text-sm">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium">{{ $order->customer_name }}</p>
                                <p class="text-xs text-gray-500">{{ $order->customer_phone }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.shops.show', $order->shop) }}" class="text-sm text-gray-600 hover:text-gray-900">
                                    {{ $order->shop->name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium">
                                {{ number_format($order->total, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full
                                    {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' :
                                       ($order->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $order->payment_status === 'paid' ? 'Payé' : ($order->payment_status === 'pending' ? 'En attente' : 'Échoué') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $order->getStatusColor() }}-100 text-{{ $order->getStatusColor() }}-800">
                                    {{ $order->getStatusLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="text-indigo-600 hover:text-indigo-800" title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-shopping-cart text-4xl mb-3 block"></i>
                                Aucune commande trouvée
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
                <div class="px-4 py-3 border-t">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
