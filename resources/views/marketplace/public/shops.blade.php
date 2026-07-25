@extends('layouts.marketplace')

@section('title', 'Nos boutiques - Seneshop')

@push('styles')
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { scrollbar-width: none; -ms-overflow-style: none; }
    </style>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-6">
        {{-- Fil d'ariane --}}
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('marketplace.public.home') }}" class="hover:text-emerald-600 transition">Accueil</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-900 font-medium">Nos boutiques</span>
        </nav>

        {{-- Bannière --}}
        <div class="relative rounded-lg overflow-hidden shadow-sm mb-6">
            <img src="{{ asset('images/boutiques-bann.png') }}" alt="Nos boutiques" class="w-full h-auto object-contain">
        </div>

        {{-- Barre de recherche --}}
        <form action="{{ route('marketplace.public.shops') }}" method="GET" class="mb-8">
            <div class="relative max-w-md">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher une boutique..."
                       class="w-full pl-10 pr-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition shadow-sm">
            </div>
        </form>

        {{-- Grille boutiques --}}
        @if($shops->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($shops as $shop)
                    <a href="{{ route('storefront.show', $shop->slug) }}"
                       class="bg-white rounded-lg shadow-sm hover:shadow-md transition p-6 text-center group border border-gray-300 hover:border-emerald-200">
                        {{-- Logo --}}
                        <div class="relative mx-auto w-20 h-20 mb-4">
                            @if($shop->logo_url)
                                <img src="{{ $shop->logo_url }}" alt="{{ $shop->name }}"
                                     class="w-20 h-20 rounded-full object-cover mx-auto group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-20 h-20 rounded-full bg-indigo-100 flex items-center justify-center mx-auto group-hover:bg-indigo-200 transition">
                                    <i class="fas fa-store text-2xl text-indigo-500"></i>
                                </div>
                            @endif

                            {{-- Badge certification --}}
                            @php
                                $shopCertification = \App\Models\Certification::where('shop_id', $shop->id)
                                    ->where('status', 'active')
                                    ->where('expires_at', '>', now())
                                    ->first();
                            @endphp
                            @if($shopCertification)
                                <span class="absolute -bottom-1 -right-1 bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow">
                                <i class="fas fa-check text-[10px]"></i>
                            </span>
                            @endif
                        </div>

                        {{-- Nom --}}
                        <h3 class="font-bold text-gray-900 group-hover:text-gray-600 transition truncate">{{ $shop->name }}</h3>



                        {{-- Stats --}}
                        <div class="flex items-center justify-center gap-4 mt-3 text-sm text-gray-500">
                        <span class="flex items-center gap-1">
                            <i class="fas fa-box text-indigo-400"></i>
                            <span class="font-medium text-gray-700">{{ $shop->products_count }}</span>
                        </span>
                            <span class="text-gray-300">|</span>
                            <span class="text-xs">produits</span>
                        </div>

                        {{-- Bouton visiter --}}
                        <div class="mt-4">
                        <span class="inline-block w-full py-2 bg-emerald-500 text-white rounded-lg text-sm font-medium group-hover:bg-gray-500 group-hover:text-white transition">
                            Visiter la boutique
                            <i class="fas fa-arrow-right ml-1 text-xs"></i>
                        </span>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $shops->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <i class="fas fa-store-slash text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-bold text-gray-600 mb-2">Aucune boutique trouvée</h3>
                <p class="text-gray-400">Essayez de modifier votre recherche.</p>
                <a href="{{ route('marketplace.public.shops') }}" class="inline-block mt-4 text-indigo-600 hover:text-indigo-700 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Voir toutes les boutiques
                </a>
            </div>
        @endif
    </div>
@endsection
