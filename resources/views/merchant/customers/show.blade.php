@extends('merchant.layouts.app')

@section('title', $customer->name ?? 'Client')
@section('header', $customer->name ?? 'Client')

@section('content')
    <div class="space-y-6">
        <a href="{{ route('merchant.customers.index', $shop) }}" class="text-emerald-600 hover:underline text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Retour à la liste
        </a>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold mb-3">Informations</h3>
                <p><strong>Nom :</strong> {{ $customer->name ?? 'N/A' }}</p>
                <p><strong>Téléphone :</strong> {{ $customer->phone }}</p>
                <p><strong>Email :</strong> {{ $customer->email ?? 'N/A' }}</p>
                <p><strong>1ère commande :</strong> {{ $firstOrder?->created_at->format('d/m/Y') ?? 'N/A' }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold mb-3">Statistiques</h3>
                <p><strong>Total commandes :</strong> {{ $customer->total_orders }}</p>
                <p><strong>Total dépensé :</strong> {{ number_format($customer->total_spent, 0, ',', ' ') }} FCFA</p>
                <p><strong>Panier moyen :</strong> {{ $customer->total_orders > 0 ? number_format($customer->total_spent / $customer->total_orders, 0, ',', ' ') : 0 }} FCFA</p>
                <p><strong>Dernière commande :</strong> {{ $customer->last_order_at?->diffForHumans() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold mb-3">Produits préférés</h3>
                @foreach($topProducts as $name => $qty)
                    <p class="text-sm">• {{ $name }} ({{ $qty }}x)</p>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold mb-4">Historique des commandes</h3>
            <table class="w-full">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">N°</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produits</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                </tr>
                </thead>
                <tbody class="divide-y">
                @foreach($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium">{{ $order->order_number }}</td>
                        <td class="px-4 py-3 text-sm">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-sm">{{ $order->items->sum('quantity') }} article(s)</td>
                        <td class="px-4 py-3 text-sm font-medium">{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                        <td class="px-4 py-3">{{ $order->getStatusLabel() }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
