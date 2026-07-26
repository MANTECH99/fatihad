@extends('layouts.marketplace')

@section('title', 'Marketplace')

@push('styles')
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { scrollbar-width: none; -ms-overflow-style: none; }

        @keyframes slideLeftRight {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .animate-slide-left-right {
            animation: slideLeftRight 15s linear infinite;
        }
    </style>
@endpush

@section('content')
    <!-- ========== DESKTOP LAYOUT (max-w-7xl) ========== -->
    <div class="hidden md:block max-w-7xl mx-auto px-4 py-6">
        <div class="grid grid-cols-12 gap-4 mb-8">
            <!-- GAUCHE -->
            <div class="col-span-2">
                <div class="bg-white rounded-lg shadow-sm h-full" style="height: 350px;">
                    <h3 class="font-bold text-lg p-4 border-b">Boutiques</h3>
                    <div class="p-2 overflow-y-auto scrollbar-hide hover:scrollbar-default" style="height: calc(350px - 60px);">
                        <button class="flex items-center space-x-3 py-3 px-2 w-full text-left hover:bg-gray-50 rounded transition bg-gray-100 font-semibold">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                                <i class="fas fa-store text-sm"></i>
                            </div>
                            <span class="text-sm truncate">Toutes les boutiques</span>
                        </button>
                        @foreach($shops as $shop)
                            <a href="{{ route('storefront.show', $shop->slug) }}" class="flex items-center space-x-3 py-3 px-2 w-full text-left hover:bg-gray-50 rounded transition border-b border-gray-100 last:border-b-0">
                                @if($shop->logo_url)
                                    <img src="{{ $shop->logo_url }}" alt="{{ $shop->name }}" class="w-8 h-8 object-cover rounded-full flex-shrink-0">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 flex-shrink-0">
                                        <i class="fas fa-store text-sm"></i>
                                    </div>
                                @endif
                                <div class="flex flex-col">
            <span class="text-sm truncate font-medium">
                {{ $shop->name }}
                @php
                    $shopCertification = \App\Models\Certification::where('shop_id', $shop->id)
                        ->where('status', 'active')
                        ->where('expires_at', '>', now())
                        ->first();
                @endphp
                @if($shopCertification)
                    <i class="fas fa-check-circle text-[9px] text-emerald-600 ml-1"></i>
                @endif
            </span>
                                    <span class="text-[10px] text-gray-400">{{ $shop->products_count }} produits</span>
                                </div>
                                {{-- SUPPRESSION du span "Certifié" ici --}}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- CENTRE -->
            <div class="col-span-8">
                <div class="rounded-lg overflow-hidden shadow-sm" style="height: 350px; position: relative;">
                    <img src="{{ asset('images/hero-bannesss.png') }}" alt="Bienvenue sur Seneshop" class="w-full h-full object-cover">
                </div>
            </div>
            <!-- DROITE -->
            <div class="col-span-2 bg-white rounded-lg shadow-sm p-4 flex flex-col justify-center" style="height: 350px;">
                <div class="space-y-4">
                    <div class="text-center border-b pb-3">
                        <div class="text-2xl mb-2">🎧</div>
                        <h4 class="font-bold text-xs">Centre d'assistance</h4>
                        <p class="text-xs text-gray-600 mt-1">Guide du service client</p>
                    </div>
                    <div class="text-center border-b pb-3">
                        <div class="text-2xl mb-2">📞</div>
                        <h4 class="font-bold text-xs">Commandez au</h4>
                        <p class="text-red-500 font-bold text-sm mt-1">33 922 56 56</p>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl mb-2">🛍️</div>
                        <h4 class="font-bold text-xs">Achetez sur Seneshop</h4>
                        <p class="text-xs text-gray-600 mt-1">Faites votre shop ici</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Changez pt-10 en pt-4 -->
        <div class="pt-4 pb-4 text-center border-b border-gray-100">
            <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-3">
                Découvrez <span style="color: #ff6b00;">nos boutiques</span>
            </h1>
            <p class="text-gray-500 text-sm mb-6">Des milliers de produits vérifiés près de chez vous.</p>

            {{-- BOUTONS CONTACTER & PARTAGER (Vert Emeraude de ton thème) --}}
            <div class="hidden md:flex justify-center gap-3 md:mt-4 md:mb-6 w-full max-w-md mx-auto">
                {{-- Bouton Contacter (Fond Vert, Texte Blanc) --}}
                <a href="https://wa.me/221772607977" target="_blank" class="flex-1 flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-4 rounded-lg transition shadow-sm">
                    <i class="fas fa-comment-alt text-lg"></i>
                    Contacter
                </a>

                {{-- Bouton Partager (Bordure Verte, Texte Vert) --}}
                <button onclick="if (navigator.share) { navigator.share({ title: 'FatiHad Marketplace', text: 'Découvrez FatiHad Marketplace !', url: window.location.href }); } else { alert('Copiez le lien : ' + window.location.href); }" class="flex-1 flex items-center justify-center gap-2 bg-transparent border-2 border-emerald-500 text-emerald-500 hover:bg-emerald-500 hover:text-white font-bold py-3 px-4 rounded-lg transition shadow-sm">
                    <i class="fas fa-share-alt text-lg"></i>
                    Partager
                </button>
            </div>



        </div>

        <!-- Desktop Boutiques -->
        <div class="max-w-7xl mx-auto px-4 py-6 mt-4">
            <div class="relative flex items-center justify-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    Découvrez nos boutiques
                    <span class="text-blue-600">certifiées</span>
                    <span class="inline-flex items-center gap-1 text-emerald-700 text-xs font-semibold px-2 py-0.5 rounded-full border border-blue-200 ml-2 align-middle">
                <i class="fas fa-check-circle text-blue-600"></i>
            </span>
                </h2>
                <a href="{{ route('marketplace.public.shops') }}" class="absolute right-0 inline-flex items-center gap-1 bg-gray-500 text-white text-xs font-semibold px-3 py-1.5 rounded-full border border-gray-200 hover:bg-emerald-200 transition whitespace-nowrap">
                    Voir plus <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach($shops->take(5) as $shop)
                    <a href="{{ route('storefront.show', $shop->slug) }}" class="bg-white rounded-lg shadow-sm hover:shadow-md transition p-4 text-center group border border-gray-200">
                        <div class="relative mx-auto w-16 h-16 mb-2">
                            @if($shop->logo_url)
                                <img src="{{ $shop->logo_url }}" alt="{{ $shop->name }}" class="w-16 h-16 rounded-full object-cover mx-auto">
                            @else
                                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto">
                                    <i class="fas fa-store text-gray-500 text-xl"></i>
                                </div>
                            @endif
                            @php
                                $shopCertification = \App\Models\Certification::where('shop_id', $shop->id)
                                    ->where('status', 'active')
                                    ->where('expires_at', '>', now())
                                    ->first();
                            @endphp
                            @if($shopCertification)
                                <span class="absolute -bottom-1 -right-1 bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center shadow">
                            <i class="fas fa-check text-[8px]"></i>
                        </span>
                            @endif
                        </div>
                        <h3 class="font-medium text-gray-900 group-hover:text-indigo-500 transition">{{ $shop->name }}</h3>
                        <p class="text-xs text-gray-400">{{ $shop->products_count }} produits</p>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Desktop Produits -->
        <div class="max-w-7xl mx-auto px-4 py-6 mt-4">
            <div class="overflow-hidden mb-6 bg-emerald-500">
                <div class="animate-slide-left-right flex whitespace-nowrap" style="width: max-content;">
                    @for ($i = 0; $i < 4; $i++)
                        <h2 class="text-xl font-bold text-white px-6 py-2 inline-block">
                            Retrouvez nos meilleurs produits et promotions
                        </h2>
                    @endfor
                </div>
            </div>
            @php
                $marketplaceProducts = \App\Models\Product::where('is_available', true)
                ->where('published_on_marketplace', true) // 🔥 UNIQUEMENT CEUX COCHÉS PAR LE MARCHAND
                    ->whereHas('shop', function ($query) {
                        $query->where('is_active', true)
                              ->where('status', 'approved')
                              ->whereHas('marketplaceSubscription', function ($subQuery) {
                                  $subQuery->where('status', 'active')
                                           ->where('expires_at', '>', now());
                              });
                    })
                    ->with(['shop', 'category', 'reviews'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            @endphp
            @if($marketplaceProducts->isNotEmpty())
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach($marketplaceProducts as $product)
                        <a href="{{ route('storefront.product', ['shop' => $product->shop->slug, 'product' => $product->id]) }}" class="bg-white rounded-lg shadow-sm hover:shadow-md transition block border border-gray-200">
                            <div class="relative w-full h-40 bg-white rounded-t-lg overflow-hidden flex items-center justify-center p-2">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="max-width:100%;max-height:100%;object-fit:contain;" loading="lazy">
                                @else
                                    <i class="fas fa-image text-4xl text-gray-400"></i>
                                @endif
                                @if($product->hasDiscount())
                                    <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">-{{ $product->discount_percentage }}%</span>
                                @endif
                                <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm rounded-full w-8 h-8 flex items-center justify-center shadow-sm text-gray-400 hover:text-red-500 transition cursor-pointer z-10">
                                    <i class="fas fa-heart text-sm"></i>
                                </div>
                            </div>
                            <div class="p-3">
                                <h3 class="font-medium text-gray-900 truncate">{{ $product->name }}</h3>
                                <p class="text-xs text-blue-700 font-medium mt-0.5 flex items-center gap-1 flex-wrap">
                                    <i class="fas fa-store mr-1 text-blue-700"></i> {{ $product->shop->name }}
                                    @php
                                        $shopCertification = \App\Models\Certification::where('shop_id', $product->shop->id)
                                            ->where('status', 'active')
                                            ->where('expires_at', '>', now())
                                            ->first();
                                    @endphp
                                    @if($shopCertification)
                                        <span class="inline-flex items-center gap-1  text-blue-700 text-[9px] font-semibold px-1.5 py-0.5 rounded-full border border-blue-200 ml-1 shadow-sm">
    <i class="fas fa-check-circle text-[9px] text-blue-600"></i>
</span>
                                    @endif
                                </p>
                                @if($product->category)
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $product->category->name }}</p>
                                @endif
                                <div class="flex items-center justify-between mt-1">
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
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-lg font-bold text-emerald-600">{{ number_format($product->current_price, 0, ',', ' ') }} FCFA</span>
                                    <span class="text-emerald-500">
                                        <i class="fas fa-shopping-cart text-sm"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-box-open text-4xl mb-2"></i>
                    <p>Aucun produit disponible pour le moment.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- ========== MOBILE LAYOUT (sans max-w-7xl) ========== -->
    <div class="block md:hidden py-6">
        <!-- Mobile Header -->
        <div class="px-4 mb-6">
            <div class="rounded-lg overflow-hidden shadow-sm" style="position: relative;">
                <img src="{{ asset('images/hero-banner-mobiles.png') }}" alt="Seneshop Marketplace" class="w-full h-48 object-cover">
            </div>
        </div>

        <!-- Mobile Logo & Info -->
            <div class="max-w-4xl mx-auto px-4 relative z-10" >
            <div class="rounded-lg p-6 pb-8">
                <div class="flex flex-col items-center space-y-4">
                    <!--     <div class="w-24 h-24 rounded-full bg-indigo-100 flex items-center justify-center border-4 border-white shadow">
                        <i class="fas fa-store text-3xl text-indigo-600"></i>
                    </div>-->
                    <div class="flex-1 text-center">
                        <h1 class="text-2xl font-bold text-gray-900">
                            FatiHad <span style="color: #ff6b00;">Marketplace</span> </h1>
                        <div class="flex flex-wrap justify-center gap-4 mt-3 text-sm text-gray-500">
                            <span class="text-green-600"><i class="fas fa-circle text-xs mr-1"></i>Ouvert</span>
                            <span><i class="fas fa-truck mr-1"></i>Livraison disponible</span>
                        </div>
                        {{-- RÉSEAUX SOCIAUX (Juste en dessous de la ville, avec les couleurs officielles) --}}
                        <div class="flex justify-center gap-3 mt-3">
                            {{-- Facebook (Bleu) --}}
                            <a href="#" target="_blank" class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#1877F2] border border-gray-200 hover:bg-[#1877F2] hover:text-white hover:border-[#1877F2] transition shadow-sm">
                                <i class="fab fa-facebook-f text-lg"></i>
                            </a>

                            {{-- TikTok (Noir officiel) --}}
                            <a href="#" target="_blank" class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#000000] border border-gray-200 hover:bg-[#000000] hover:text-white hover:border-[#000000] transition shadow-sm">
                                <i class="fab fa-tiktok text-lg"></i>
                            </a>

                            {{-- Instagram (Dégradé rose/orange) --}}
                            <a href="#" target="_blank" class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#E1306C] border border-gray-200 hover:bg-[#E1306C] hover:text-white hover:border-[#E1306C] transition shadow-sm">
                                <i class="fab fa-instagram text-lg"></i>
                            </a>

                            {{-- WhatsApp (Vert) --}}
                            <a href="https://wa.me/221700000000" target="_blank" class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#25D366] border border-gray-200 hover:bg-[#25D366] hover:text-white hover:border-[#25D366] transition shadow-sm">
                                <i class="fab fa-whatsapp text-lg"></i>
                            </a>
                        </div>
                        <div class="flex justify-center gap-3 mt-4 w-full max-w-md mx-auto">
                            <a href="#" class="flex-1 flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-4 rounded-lg transition shadow-sm">
                                <i class="fas fa-comment-alt text-lg"></i>
                                Contacter
                            </a>
                            <button onclick="if (navigator.share) { navigator.share({ title: 'Seneshop Marketplace', text: 'Découvrez Seneshop Marketplace !', url: window.location.href }); }" class="flex-1 flex items-center justify-center gap-2 bg-transparent border-2 border-emerald-500 text-emerald-500 hover:bg-emerald-500 hover:text-white font-bold py-3 px-4 rounded-lg transition shadow-sm">
                                <i class="fas fa-share-alt text-lg"></i>
                                Partager
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Boutiques -->
        <div class="px-4 py-6">
            <h2 class="text-2xl font-bold text-center text-gray-900 mb-6">
                Nos boutiques
                <span class="text-blue-500">certifiées</span>
                <span class="inline-flex items-center gap-1 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded-full border border-blue-200 ml-2 align-middle">
            <i class="fas fa-check-circle text-blue-600"></i>
        </span>
            </h2>
            <div class="overflow-hidden">
                <div class="animate-slide-left-right flex gap-3" style="width: max-content;">
                    @for ($i = 0; $i < 2; $i++)
                        @foreach($shops->take(5) as $shop)
                            <a href="{{ route('storefront.show', $shop->slug) }}" class="bg-white rounded-lg shadow-sm hover:shadow-md transition p-4 text-center group flex-shrink-0 w-[160px] border border-gray-200">
                                <div class="relative mx-auto w-16 h-16 mb-2">
                                    @if($shop->logo_url)
                                        <img src="{{ $shop->logo_url }}" alt="{{ $shop->name }}" class="w-16 h-16 rounded-full object-cover mx-auto">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto">
                                            <i class="fas fa-store text-gray-500 text-xl"></i>
                                        </div>
                                    @endif
                                    @php
                                        $shopCertification = \App\Models\Certification::where('shop_id', $shop->id)
                                            ->where('status', 'active')
                                            ->where('expires_at', '>', now())
                                            ->first();
                                    @endphp
                                    @if($shopCertification)
                                        <span class="absolute -bottom-1 -right-1 bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center shadow">
                                    <i class="fas fa-check text-[8px]"></i>
                                </span>
                                    @endif
                                </div>
                                <h3 class="font-medium text-gray-900 group-hover:text-indigo-500 transition">{{ $shop->name }}</h3>
                                <p class="text-xs text-gray-400">{{ $shop->products_count }} produits</p>
                            </a>
                        @endforeach
                    @endfor
                </div>
            </div>
            <div class="mt-8">
                {{ $shops->links() }}
            </div>
        </div>

        <!-- Mobile Produits -->
        <div class="px-4 pt-0 pb-6 mt-0">
            <div class="overflow-hidden mb-6 bg-emerald-500">
                <div class="animate-slide-left-right flex whitespace-nowrap" style="width: max-content;">
                    @for ($i = 0; $i < 4; $i++)
                        <h2 class="text-lg font-bold text-white px-4 py-2 inline-block">
                            Nos meilleurs produits
                        </h2>
                    @endfor
                </div>
            </div>
            @if($marketplaceProducts->isNotEmpty())
                <div class="grid grid-cols-2 gap-3">
                    @foreach($marketplaceProducts as $product)
                        <a href="{{ route('storefront.product', ['shop' => $product->shop->slug, 'product' => $product->id]) }}" class="bg-white rounded-lg shadow-sm hover:shadow-md transition block border border-gray-200">
                            <div class="relative w-full h-40 bg-white rounded-t-lg overflow-hidden flex items-center justify-center p-2">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="max-width:100%;max-height:100%;object-fit:contain;" loading="lazy">
                                @else
                                    <i class="fas fa-image text-4xl text-gray-400"></i>
                                @endif
                                @if($product->hasDiscount())
                                    <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">-{{ $product->discount_percentage }}%</span>
                                @endif
                                <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm rounded-full w-8 h-8 flex items-center justify-center shadow-sm text-gray-400 hover:text-red-500 transition cursor-pointer z-10">
                                    <i class="fas fa-heart text-sm"></i>
                                </div>
                            </div>
                            <div class="p-3">
                                <h3 class="font-medium text-gray-900 truncate">{{ $product->name }}</h3>
                                <p class="text-xs text-blue-600 font-medium mt-0.5 flex items-center gap-1 flex-wrap">
                                    <i class="fas fa-store mr-1 text-blue-600"></i> {{ $product->shop->name }}
                                    @php
                                        $productShopCertification = \App\Models\Certification::where('shop_id', $product->shop->id)
                                            ->where('status', 'active')
                                            ->where('expires_at', '>', now())
                                            ->first();
                                    @endphp
                                    @if($productShopCertification)
                                        <span class="inline-flex items-center gap-0.5 md:gap-1  text-blue-700 text-[8px] md:text-[9px] font-semibold px-1 md:px-1.5 py-0.5 rounded-full border border-blue-200 ml-1 shadow-sm">
        <i class="fas fa-check-circle text-[7px] md:text-[9px] text-blue-600"></i>
    </span>
                                    @endif
                                </p>
                                @if($product->category)
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $product->category->name }}</p>
                                @endif
                                <div class="flex items-center justify-between mt-1">
                                    @if($product->track_inventory)
                                        <span class="text-xs {{ $product->stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $product->stock > 0 ? 'En stock' : 'Rupture' }}
                                </span>
                                    @else
                                        <span class="text-xs text-green-600">En stock</span>
                                    @endif
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
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-lg font-bold text-emerald-600">{{ number_format($product->current_price, 0, ',', ' ') }} FCFA</span>
                                    <span class="text-emerald-500">
                                        <i class="fas fa-shopping-cart text-sm"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-box-open text-4xl mb-2"></i>
                    <p>Aucun produit disponible pour le moment.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Alpine.js si besoin
    </script>
@endpush
