@extends('merchant.layouts.app')

@section('title', 'Mes boutiques')
@section('header', 'Mes boutiques')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">Mes boutiques</h2>
        <a href="{{ route('merchant.shops.create') }}" class="bg-emerald-500 text-white px-4 py-2 rounded-md text-sm hover:bg-emerald-600">
            <i class="fas fa-plus mr-2"></i> Nouvelle boutique
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($shops as $shop)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center gap-3 mb-3">
                    @if($shop->logo_url)
                        <img src="{{ $shop->logo_url }}" class="w-10 h-10 rounded-full object-cover">
                    @else
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                            <i class="fas fa-store text-emerald-600"></i>
                        </div>
                    @endif
                    <div>
                        <h3 class="font-medium text-gray-900">{{ $shop->name }}</h3>
                        <p class="text-xs text-gray-500">{{ $shop->city ?? 'Sans ville' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 text-xs text-gray-500 mb-3">
                    <span><i class="fas fa-box mr-1"></i> {{ $shop->products_count }} produits</span>
                    <span><i class="fas fa-shopping-cart mr-1"></i> {{ $shop->orders_count }} commandes</span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('merchant.shops.edit', $shop) }}" class="flex-1 text-center px-3 py-1.5 bg-gray-100 rounded-md text-xs hover:bg-gray-200">
                        <i class="fas fa-edit mr-1"></i> Modifier
                    </a>
                    <a href="{{ route('merchant.products.index', $shop) }}" class="flex-1 text-center px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-md text-xs hover:bg-emerald-100">
                        <i class="fas fa-box mr-1"></i> Produits
                    </a>
                    <a href="{{ route('storefront.show', $shop->slug) }}" target="_blank" class="px-3 py-1.5 bg-gray-100 rounded-md text-xs hover:bg-gray-200">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-store text-5xl text-gray-300 mb-4 block"></i>
                <p class="text-gray-500">Aucune boutique pour le moment.</p>
                <a href="{{ route('merchant.shops.create') }}" class="mt-4 inline-block bg-emerald-500 text-white px-6 py-2 rounded-md text-sm">Créer une boutique</a>
            </div>
        @endforelse
    </div>
@endsection
