{{-- resources/views/admin/shops/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Boutique - ' . $shop->name)
@section('header', $shop->name)

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne gauche -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Infos boutique -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center space-x-4 mb-6">
                    <img src="{{ $shop->logo_url ?? 'https://placehold.co/100x100?text=' . substr($shop->name, 0, 1) }}"
                         class="w-16 h-16 rounded-full">
                    <div>
                        <h2 class="text-xl font-bold">{{ $shop->name }}</h2>
                        <p class="text-gray-500">{{ $shop->city ?? 'Ville non spécifiée' }}</p>
                    </div>
                    <div class="ml-auto">
                        @if($shop->status === 'pending')
                            <form action="{{ route('admin.shops.approve', $shop) }}" method="POST" class="inline">
                                @csrf
                                <button class="bg-green-500 text-white px-3 py-1.5 rounded-md text-sm mr-2">
                                    <i class="fas fa-check mr-1"></i> Approuver
                                </button>
                            </form>
                            <form action="{{ route('admin.shops.reject', $shop) }}" method="POST" class="inline">
                                @csrf
                                <button class="bg-red-500 text-white px-3 py-1.5 rounded-md text-sm">
                                    <i class="fas fa-times mr-1"></i> Rejeter
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-2xl font-bold">{{ $shop->products_count }}</p>
                        <p class="text-xs text-gray-500">Produits</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-2xl font-bold">{{ $shop->orders_count }}</p>
                        <p class="text-xs text-gray-500">Commandes</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-2xl font-bold">{{ $shop->categories_count }}</p>
                        <p class="text-xs text-gray-500">Catégories</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-2xl font-bold">⭐ {{ number_format($shop->average_rating, 1) }}</p>
                        <p class="text-xs text-gray-500">Note</p>
                    </div>
                </div>
            </div>

            <!-- Dernières commandes -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b">
                    <h3 class="font-semibold">Dernières commandes</h3>
                </div>
                <div class="p-6">
                    @if($shop->orders->isNotEmpty())
                        <table class="w-full">
                            <thead>
                            <tr class="text-left text-sm text-gray-500">
                                <th class="pb-2">N°</th>
                                <th class="pb-2">Client</th>
                                <th class="pb-2">Total</th>
                                <th class="pb-2">Statut</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($shop->orders->take(10) as $order)
                                <tr class="border-t">
                                    <td class="py-2 text-sm">{{ $order->order_number }}</td>
                                    <td class="py-2 text-sm">{{ $order->customer_name }}</td>
                                    <td class="py-2 text-sm">{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                                    <td class="py-2">
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-{{ $order->getStatusColor() }}-100 text-{{ $order->getStatusColor() }}-800">
                                            {{ $order->getStatusLabel() }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500 text-center py-4">Aucune commande</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Colonne droite -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold mb-4">Propriétaire</h3>
                <p class="font-medium">{{ $shop->user->name }}</p>
                <p class="text-sm text-gray-500">{{ $shop->user->email }}</p>
                <p class="text-sm text-gray-500">{{ $shop->user->phone }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold mb-4">Statut</h3>
                <div class="space-y-2">
                    <p>
                        <strong>Validation :</strong>
                        <span class="px-2 py-0.5 text-xs rounded-full
                        {{ $shop->status === 'approved' ? 'bg-green-100 text-green-800' :
                           ($shop->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ $shop->status === 'approved' ? 'Approuvée' : ($shop->status === 'pending' ? 'En attente' : 'Rejetée') }}
                    </span>
                    </p>
                    <p><strong>Active :</strong> {{ $shop->is_active ? '✅ Oui' : '❌ Non' }}</p>
                    <p><strong>Ouverte :</strong> {{ $shop->is_open ? '✅ Oui' : '❌ Non' }}</p>
                    <p><strong>Créée le :</strong> {{ $shop->created_at->format('d/m/Y') }}</p>
                </div>

                <div class="mt-4 space-y-2">
                    <form action="{{ route('admin.shops.toggle-active', $shop) }}" method="POST">
                        @csrf
                        <button class="w-full px-4 py-2 bg-yellow-500 text-white rounded-md text-sm hover:bg-yellow-600">
                            {{ $shop->is_active ? 'Désactiver' : 'Activer' }} la boutique
                        </button>
                    </form>
                    <a href="{{ route('storefront.show', $shop->slug) }}" target="_blank"
                       class="block w-full px-4 py-2 bg-blue-500 text-white rounded-md text-sm hover:bg-blue-600 text-center">
                        <i class="fas fa-external-link-alt mr-1"></i> Voir la boutique
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold mb-4">Contact</h3>
                <p><i class="fab fa-whatsapp text-green-500 mr-2"></i> {{ $shop->whatsapp_phone }}</p>
                @if($shop->contact_phone)
                    <p class="mt-1"><i class="fas fa-phone mr-2"></i> {{ $shop->contact_phone }}</p>
                @endif
                @if($shop->contact_email)
                    <p class="mt-1"><i class="fas fa-envelope mr-2"></i> {{ $shop->contact_email }}</p>
                @endif
                @if($shop->address)
                    <p class="mt-1"><i class="fas fa-map-marker-alt mr-2"></i> {{ $shop->address }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection
