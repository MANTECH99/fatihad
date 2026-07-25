@extends('layouts.marketplace')

@section('title', 'Promotions - Seneshop')

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
            <span class="text-gray-900 font-medium">Promotions</span>
        </nav>

        <div class="flex gap-6">
            {{-- SIDEBAR CATÉGORIES --}}
            <div class="hidden md:block w-64 flex-shrink-0">
                <div class="bg-white rounded-lg shadow-sm p-4 sticky top-28">
                    <h3 class="font-bold text-lg mb-4 pb-3 border-b flex items-center gap-2">
                        <i class="fas fa-tag text-emerald-500"></i> Catégories en promo
                    </h3>

                    {{-- Toutes les catégories --}}
                    <a href="{{ route('marketplace.public.promotions') }}"
                       class="flex items-center justify-between py-2.5 px-3 rounded-lg mb-1 transition {{ !request('category') ? 'bg-emerald-50 text-emerald-600 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
            <span class="flex items-center gap-2">
                <i class="fas fa-border-all text-sm"></i> Toutes
            </span>
                        <span class="text-xs bg-gray-100 {{ !request('category') ? 'bg-emerald-200' : '' }} px-2 py-0.5 rounded-full">{{ $totalProducts }}</span>
                    </a>

                    @foreach($categories as $category)
                        <a href="{{ route('marketplace.public.promotions', ['category' => $category->id]) }}"
                           class="flex items-center justify-between py-2.5 px-3 rounded-lg mb-1 transition {{ request('category') == $category->id ? 'bg-red-50 text-red-600 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                <span class="flex items-center gap-2 truncate">
                    <i class="fas fa-folder text-sm text-gray-400"></i> {{ $category->name }}
                </span>
                            <span class="text-xs bg-gray-100 {{ request('category') == $category->id ? 'bg-red-200' : '' }} px-2 py-0.5 rounded-full flex-shrink-0">{{ $category->products_count }}</span>
                        </a>
                    @endforeach

                    {{-- FILTRE PAR PRIX --}}
                    <h3 class="font-bold text-lg mb-4 pb-3 border-b flex items-center gap-2 mt-6">
                        <i class="fas fa-filter text-red-500"></i> Prix
                    </h3>

                    <form action="{{ route('marketplace.public.promotions') }}" method="GET" class="space-y-3">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif

                        <div>
                            <label class="text-xs text-gray-500 font-medium">Prix minimum (FCFA)</label>
                            <input type="number" name="prix_min" value="{{ request('prix_min') }}" placeholder="Min"
                                   class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-red-500 focus:border-red-500">
                        </div>

                        <div>
                            <label class="text-xs text-gray-500 font-medium">Prix maximum (FCFA)</label>
                            <input type="number" name="prix_max" value="{{ request('prix_max') }}" placeholder="Max"
                                   class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-red-500 focus:border-red-500">
                        </div>

                        <button type="submit" class="w-full bg-red-500 text-white text-sm font-medium py-2 rounded-lg hover:bg-red-600 transition">
                            <i class="fas fa-search mr-1"></i> Filtrer
                        </button>

                        @if(request('prix_min') || request('prix_max'))
                            <a href="{{ route('marketplace.public.promotions', request()->only('category')) }}" class="block text-center text-xs text-gray-500 hover:text-gray-700 mt-1">
                                <i class="fas fa-times mr-1"></i> Réinitialiser
                            </a>
                        @endif
                    </form>

                    {{-- TRI PAR PRIX --}}
                    <h3 class="font-bold text-lg mb-4 pb-3 border-b flex items-center gap-2 mt-6">
                        <i class="fas fa-sort text-red-500"></i> Trier par prix
                    </h3>

                    <div class="space-y-2">
                        <a href="{{ route('marketplace.public.promotions', array_merge(request()->except('tri'), ['tri' => 'asc'])) }}"
                           class="flex items-center justify-between py-2.5 px-3 rounded-lg transition {{ request('tri') == 'asc' ? 'bg-red-50 text-red-600 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                <span class="flex items-center gap-2">
                    <i class="fas fa-arrow-up text-sm"></i> Croissant
                </span>
                        </a>

                        <a href="{{ route('marketplace.public.promotions', array_merge(request()->except('tri'), ['tri' => 'desc'])) }}"
                           class="flex items-center justify-between py-2.5 px-3 rounded-lg transition {{ request('tri') == 'desc' ? 'bg-red-50 text-red-600 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                <span class="flex items-center gap-2">
                    <i class="fas fa-arrow-down text-sm"></i> Décroissant
                </span>
                        </a>

                        @if(request('tri'))
                            <a href="{{ route('marketplace.public.promotions', request()->except('tri')) }}" class="block text-center text-xs text-gray-500 hover:text-gray-700 mt-1">
                                <i class="fas fa-times mr-1"></i> Réinitialiser
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- CONTENU PRINCIPAL --}}
            <div class="flex-1 min-w-0">
                {{-- En-tête --}}
                <div class="relative rounded-lg overflow-hidden shadow-sm mb-6">
                    <img src="{{ asset('images/promotions-bann.png') }}" alt="Promotions" class="w-full h-auto object-contain">
                </div>

                {{-- Grille produits --}}
                @if($products->isNotEmpty())
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($products as $product)
                            <a href="{{ route('storefront.product', ['shop' => $product->shop->slug, 'product' => $product->id]) }}"
                               class="bg-white rounded-lg shadow-sm hover:shadow-md transition block group border border-gray-200">
                                <div class="relative w-full h-48 bg-white rounded-t-lg overflow-hidden flex items-center justify-center p-3">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                             class="max-w-full max-h-full object-contain group-hover:scale-105 transition duration-300" loading="lazy">
                                    @else
                                        <i class="fas fa-image text-4xl text-gray-400"></i>
                                    @endif
                                    {{-- Badge promo --}}
                                    <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full font-bold z-10">
                                    -{{ $product->discount_percentage }}%
                                </span>
                                    {{-- Prix barré --}}
                                    <span class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm text-gray-400 text-xs px-2 py-1 rounded-full line-through z-10">
                                    {{ number_format($product->price, 0, ',', ' ') }} FCFA
                                </span>
                                </div>
                                <div class="p-3">
                                    <h3 class="font-medium text-gray-900 truncate group-hover:text-emerald-600 transition">{{ $product->name }}</h3>
                                    <p class="text-xs text-blue-600 font-medium mt-1 flex items-center gap-1 flex-wrap">
                                        <i class="fas fa-store text-blue-600"></i> {{ $product->shop->name }}
                                        @php
                                            $shopCertification = \App\Models\Certification::where('shop_id', $product->shop->id)
                                                ->where('status', 'active')
                                                ->where('expires_at', '>', now())
                                                ->first();
                                        @endphp
                                        @if($shopCertification)
                                            <span class="inline-flex items-center gap-1  text-blue-700 text-[9px] font-semibold px-1.5 py-0.5 rounded-full border border-blue-200">
                                            <i class="fas fa-check-circle text-[9px] text-blue-600"></i>
                                        </span>
                                        @endif
                                    </p>
                                    @if($product->category)
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $product->category->name }}</p>
                                    @endif
                                    <div class="flex items-center justify-between mt-2">
                                        @if($product->track_inventory)
                                            <span class="text-xs {{ $product->stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                                            {{ $product->stock > 0 ? 'En stock' : 'Rupture' }}
                                        </span>
                                        @else
                                            <span class="text-xs text-green-600">En stock</span>
                                        @endif
                                        @php
                                            $avg = $product->reviews()->where('is_approved', true)->avg('rating') ?? 0;
                                            $count = $product->reviews()->where('is_approved', true)->count();
                                        @endphp
                                        @if($count > 0)
                                            <div class="flex items-center gap-1">
                                                <div class="flex text-yellow-400 text-xs">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        {{ $i <= round($avg) ? '★' : '☆' }}
                                                    @endfor
                                                </div>
                                                <span class="text-xs text-gray-400">({{ $count }})</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-between mt-3">
                                        <div>
                                            <span class="text-lg font-bold text-emerald-600">{{ number_format($product->sale_price, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                        <span class="bg-emerald-500 text-white w-8 h-8 rounded-full flex items-center justify-center hover:bg-red-600 transition">
                                        <i class="fas fa-shopping-cart text-sm"></i>
                                    </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                        <i class="fas fa-tag text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-bold text-gray-600 mb-2">Aucune promotion disponible</h3>
                        <p class="text-gray-400">Revenez bientôt pour découvrir nos offres.</p>
                        <a href="{{ route('marketplace.public.all-products') }}" class="inline-block mt-4 text-red-600 hover:text-red-700 font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>Voir tous les produits
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
