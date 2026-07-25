{{-- resources/views/storefront/show.blade.php --}}
@extends('layouts.storefront')

@section('title', $shop->name)

@push('styles')
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
    </style>
@endpush

@section('content')
    <div x-data="shopApp()" x-init="init()">
        <!-- Header -->
        <div class="relative">

            <!-- ========== DESKTOP LAYOUT (S'AF FICHE SUR GRAND ÉCRAN) ========== -->
            <div class="hidden md:block">
                <div class="max-w-7xl mx-auto px-4 py-6">
                    <div class="grid grid-cols-12 gap-4 mb-8">

                        <!-- Gauche : Sidebar Catégories -->
                        <!-- Gauche : Sidebar Catégories -->
                        <div class="col-span-2">
                            <div class="bg-white rounded-lg shadow-sm h-full" style="height: 350px;">
                                <h3 class="font-bold text-lg p-4 border-b">Catégories</h3>
                                <div class="p-2 overflow-y-auto scrollbar-hide hover:scrollbar-default" style="height: calc(350px - 60px);">

                                    {{-- Bouton "Tout voir" (pas de border-b car c'est le premier) --}}
                                    <button @click="activeCategory = 'all'"
                                            :class="activeCategory === 'all' ? 'bg-gray-100 font-semibold' : ''"
                                            class="flex items-center space-x-3 py-3 px-2 w-full text-left hover:bg-gray-50 rounded transition">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                                            <i class="fas fa-th-large text-sm"></i>
                                        </div>
                                        <span class="text-sm truncate">Tout voir</span>
                                    </button>

                                    {{-- Boucle des catégories --}}
                                    @foreach($categories->take(10) as $category)
                                        <button @click="activeCategory = 'cat-{{ $category->id }}'"
                                                :class="activeCategory === 'cat-{{ $category->id }}' ? 'bg-gray-100 font-semibold' : ''"
                                                class="flex items-center space-x-3 py-3 px-2 w-full text-left hover:bg-gray-50 rounded transition {{ !$loop->last ? 'border-b' : '' }}">

                                            {{-- Vérification de l'image de la catégorie --}}
                                            @if($category->image)
                                                <img src="{{ asset('storage/' . $category->image) }}"
                                                     alt="{{ $category->name }}" {{-- CORRIGÉ : name au lieu de nom --}}
                                                     class="w-8 h-8 object-cover rounded-full flex-shrink-0">
                                            @else
                                                {{-- Si pas d'image, on affiche une icône par défaut --}}
                                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 flex-shrink-0">
                                                    <i class="fas fa-tag text-sm"></i>
                                                </div>
                                            @endif

                                            <span class="text-sm truncate">{{ $category->name }}</span> {{-- CORRIGÉ : name au lieu de nom --}}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Centre : COVER IMAGE -->
                        <div class="col-span-8">
                            <div class="rounded-lg overflow-hidden shadow-sm" style="height: 350px; position: relative; background-color: #f3f4f6;">
                                @if($shop->cover_image_url)
                                    <img src="{{ $shop->cover_image_url }}"
                                         alt="{{ $shop->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-r from-indigo-500 to-emerald-500 flex items-center justify-center text-white text-center p-8 relative">
                                        <div class="absolute inset-0 bg-black/20"></div>
                                        <div class="relative z-10">
                                            <h1 class="text-4xl font-bold mb-2">Bienvenue sur {{ $shop->name }}</h1>
                                            <p class="text-lg opacity-90">Découvrez nos catégories et leurs produits</p>
                                            <div class="mt-4 flex justify-center gap-4 text-sm">
                                                <span class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">+ {{ $categories->count() }} catégories</span>
                                                <span class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">+ {{ $shop->products->count() }} produits</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Droite : Infos -->
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
                                    <h4 class="font-bold text-xs">Achetez sur {{ $shop->name }}</h4>
                                    <p class="text-xs text-gray-600 mt-1">Faites votre shop ici</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- ========== MOBILE LAYOUT (S'AFFICHE SUR PETIT ÉCRAN) ========== -->
            <div class="block md:hidden">
                <div class="container mx-auto px-4 py-6">
                    <div class="mb-6">
                        <div class="rounded-lg overflow-hidden shadow-sm" style="position: relative;">
                            @if($shop->cover_image_url)
                                <img src="{{ $shop->cover_image_url }}" alt="{{ $shop->name }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gradient-to-r from-indigo-500 to-emerald-500 flex items-center justify-center text-white text-center p-6 relative">
                                    <div class="absolute inset-0 bg-black/20"></div>
                                    <div class="relative z-10">
                                        <h1 class="text-xl font-bold mb-1">Welcome to {{ $shop->name }}</h1>
                                        <p class="text-sm opacity-90">Découvrez nos produits et catégories</p>
                                        <div class="mt-3 flex justify-center gap-3 text-xs">
                                            <span class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full">{{ $categories->count() }} catégories</span>
                                            <span class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full">{{ $shop->products->count() }} produits</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========== LOGO & INFO (UNIQUEMENT SUR MOBILE) ========== si on veut que le logo descend -->
            <div class="block md:hidden max-w-4xl mx-auto px-4 relative z-10" style="transform: translateY(-60px);">
                <!--   <div class="block md:hidden max-w-4xl mx-auto px-4 -mt-20 relative z-10">-->
                <div class="rounded-lg  p-6 pb-8"> {{-- Ajout de pb-8 pour que le logo ne soit pas collé au bord bas --}}
                    <div class="flex flex-col items-center space-y-4">
                        @if($shop->logo_url)
                            <img src="{{ $shop->logo_url }}" alt="{{ $shop->name }}" class="w-24 h-24 rounded-full border-4 border-white shadow {{---mt-12--}}"> {{-- et ici on supprime -mt-12 si on veut que le logo descend  --}}
                        @else
                            <div class="w-24 h-24 rounded-full bg-emerald-100 flex items-center justify-center border-4 border-white shadow -mt-12"><i class="fas fa-store text-3xl text-emerald-600"></i></div>
                        @endif
                        <div class="flex-1 text-center">

                            {{-- ★★★ AJOUT BADGE CERTIFICATION ICI ★★★ --}}
                            @php
                                $certification = \App\Models\Certification::where('shop_id', $shop->id)
                                    ->where('status', 'active')
                                    ->where('expires_at', '>', now())
                                    ->first();
                            @endphp

                            @if($certification)
                                <div class="inline-flex items-center gap-2 bg-green-50 text-green-700 border border-green-200 rounded-full px-4 py-1 mb-2">
                                    <i class="fas fa-shield-alt text-green-600"></i>
                                    <span class="text-sm font-semibold">
                                        {{ \App\Services\PlanService::$certifications[$certification->plan]['name'] }}
                                    </span>
                                </div>
                            @endif
                            {{-- ★★★ FIN AJOUT BADGE ★★★ --}}
                            <h1 class="text-2xl font-bold text-gray-900">{{ $shop->name }}</h1>

                            {{-- ★★★ AVIS ET ÉTOILES ICI (Juste sous le nom) ★★★ --}}
                            <div class="flex flex-col items-center mt-1">
                                <div class="flex items-center gap-1">
                                    <div class="flex text-yellow-400 text-sm">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($shop->average_rating))
                                                ★
                                            @else
                                                ☆
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ number_format($shop->average_rating, 1) }}
                                        <span class="text-gray-400 text-xs">({{ $shop->reviews->count() }} avis)</span>
                                    </span>
                                </div>
                            </div>
                            {{-- FIN DES ÉTOILES --}}
                            @if($shop->description)<p class="text-gray-600 mt-1">{{ $shop->description }}</p>@endif
                            <div class="flex flex-wrap justify-center gap-4 mt-3 text-sm text-gray-500">
                                @if($shop->city)<span><i class="fas fa-map-marker-alt text-emerald-500 mr-1"></i>{{ $shop->city }}</span>@endif

                                    {{--     <span class="{{ $shop->is_open ? 'text-green-600' : 'text-red-600' }}"><i class="fas fa-circle text-xs mr-1"></i>{{ $shop->is_open ? 'Ouvert' : 'Fermé' }}</span>
        <span><i class="fas fa-truck mr-1"></i>{{ $shop->delivery_fee > 0 ? 'Livraison '.number_format($shop->delivery_fee, 0, ',', ' ').' FCFA' : 'Livraison gratuite' }}</span>
        @if($shop->min_order > 0)<span><i class="fas fa-shopping-basket mr-1"></i>Min. {{ number_format($shop->min_order, 0, ',', ' ') }} FCFA</span>@endif--}}
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

                                    {{-- BOUTONS CONTACTER & PARTAGER (Vert Emeraude de ton thème) --}}
                                    <div class="flex justify-center gap-3 mt-4 w-full max-w-md mx-auto">
                                        {{-- Bouton Contacter (Fond Vert, Texte Blanc) --}}
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $shop->whatsapp_phone) }}" target="_blank" class="flex-1 flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-4 rounded-lg transition shadow-sm">
                                            <i class="fas fa-comment-alt text-lg"></i>
                                            Contacter
                                        </a>

                                        {{-- Bouton Partager (Bordure Verte, Texte Vert) --}}
                                        <button onclick="if (navigator.share) { navigator.share({ title: '{{ $shop->name }}', text: 'Découvrez {{ $shop->name }} !', url: window.location.href }); } else { alert('Copiez le lien : ' + window.location.href); }" class="flex-1 flex items-center justify-center gap-2 bg-transparent border-2 border-emerald-500 text-emerald-500 hover:bg-emerald-500 hover:text-white font-bold py-3 px-4 rounded-lg transition shadow-sm">
                                            <i class="fas fa-share-alt text-lg"></i>
                                            Partager
                                        </button>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contenu principal -->
                <!-- Contenu principal -->
                <!-- Contenu principal -->
                <div class="max-w-7xl mx-auto px-4 py-6 -mt-8 pb-24 md:pb-6">
                    <div x-show="currentTab === 'menu'">
                        @if($categories->isNotEmpty())
                            <!-- ========================================== -->
                            <!-- SECTION HERO (Titre + Recherche + Catégories)-->
                            <!-- ========================================== -->
                            <!-- Changez pt-10 en pt-4 -->
                            <div class="pt-4 pb-4 text-center border-b border-gray-100">
                                <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-3">
                                    Découvrez <span style="color: #ff6b00;">l'exceptionnel</span>
                                </h1>
                                <p class="text-gray-500 text-sm mb-6">Des milliers de produits vérifiés près de chez vous.</p>

                                {{-- BOUTONS CONTACTER & PARTAGER (Vert Emeraude de ton thème) --}}
                                <div class="hidden md:flex justify-center gap-3 md:mt-4 md:mb-6 w-full max-w-md mx-auto">
                                    {{-- Bouton Contacter (Fond Vert, Texte Blanc) --}}
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $shop->whatsapp_phone) }}" target="_blank" class="flex-1 flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-4 rounded-lg transition shadow-sm">
                                        <i class="fas fa-comment-alt text-lg"></i>
                                        Contacter
                                    </a>

                                    {{-- Bouton Partager (Bordure Verte, Texte Vert) --}}
                                    <button onclick="if (navigator.share) { navigator.share({ title: '{{ $shop->name }}', text: 'Découvrez {{ $shop->name }} !', url: window.location.href }); } else { alert('Copiez le lien : ' + window.location.href); }" class="flex-1 flex items-center justify-center gap-2 bg-transparent border-2 border-emerald-500 text-emerald-500 hover:bg-emerald-500 hover:text-white font-bold py-3 px-4 rounded-lg transition shadow-sm">
                                        <i class="fas fa-share-alt text-lg"></i>
                                        Partager
                                    </button>
                                </div>

                                <!-- Recherche Centrale (Bouton VERT) -->
                                <div class="max-w-xl mx-auto px-4 mb-6">
                                    <div class="flex bg-gray-100 rounded-full overflow-hidden shadow-sm focus-within:ring-0">
                                        <div class="flex items-center pl-4 pr-2 text-gray-400"><i class="fas fa-search text-sm"></i></div>
                                        <input type="text" placeholder="Rechercher un iPhone, une robe..." class="search-input flex-1 w-full h-full py-3 text-sm text-gray-700 placeholder-gray-400" />
                                        <button class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 text-sm flex items-center font-medium transition-colors">Chercher <i class="fas fa-arrow-right ml-2"></i></button>
                                    </div>
                                </div>

                                <!-- Pills Catégories (Défilement horizontal + Fond vert) -->
                                <div class="flex overflow-x-auto space-x-2 pb-2 px-4 mt-2 scrollbar-hide max-w-4xl mx-auto">
                                    <!-- Bouton Tout voir -->
                                    <button @click="activeCategory = 'all'" :class="activeCategory === 'all' ? 'bg-emerald-500 text-white' : 'bg-white border border-gray-200 text-gray-700'" class="px-5 py-2 rounded-full text-xs font-medium whitespace-nowrap shadow-sm transition-colors flex-shrink-0">
                                        Tout voir
                                    </button>

                                    <!-- Boucle dynamique des catégories -->
                                    @foreach($categories as $category)
                                        <button @click="activeCategory = 'cat-{{ $category->id }}'" :class="activeCategory === 'cat-{{ $category->id }}' ? 'bg-emerald-500 text-white' : 'bg-white border border-gray-200 text-gray-700'" class="px-4 py-2 rounded-full text-xs font-medium flex items-center whitespace-nowrap shadow-sm transition-colors flex-shrink-0">
                                            <i class="fas fa-tag text-gray-400 mr-2 text-xs"></i>
                                            {{ $category->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @foreach($categories as $category)

                                {{-- Affichage normal dans "Tout voir" : uniquement les catégories avec produits --}}
                        @if($category->products->count() > 0)
                            <div
                                x-show="activeCategory === 'all' || activeCategory === 'cat-{{ $category->id }}'"
                                class="mb-8"
                            >
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                                    {{ $category->name }}
                                </h2>

                                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                    @foreach($category->products as $product)
                                        @include('storefront.partials.product-card', [
                                            'product' => $product,
                                            'shop' => $shop
                                        ])
                                    @endforeach
                                </div>
                            </div>

                        @else
                            {{-- Catégorie vide : affichée uniquement lorsqu'elle est sélectionnée --}}
                            <div
                                x-show="activeCategory === 'cat-{{ $category->id }}'"
                                class="mb-8"
                                x-cloak
                            >
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                                    {{ $category->name }}
                                </h2>

                                <div class="bg-gray-50 border border-dashed border-gray-300 rounded-xl py-12 text-center">
                                    <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>

                                    <h3 class="text-lg font-semibold text-gray-700">
                                        Aucun produit disponible
                                    </h3>

                                    <p class="text-gray-500 mt-2">
                                        Cette catégorie ne contient aucun produit pour le moment.
                                    </p>
                                </div>
                            </div>
                        @endif

                    @endforeach
                @else
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @php $allProducts = \App\Models\Product::where('shop_id', $shop->id)->where('is_available', true)->orderBy('order')->get(); @endphp
                        @foreach($allProducts as $product)
                            @include('storefront.partials.product-card', ['product' => $product, 'shop' => $shop])
                        @endforeach
                    </div>
                @endif
            </div>

            <div x-show="currentTab === 'reviews'" x-cloak>
                <h2 class="text-xl font-semibold mb-6">Avis clients</h2>

                {{-- Formulaire --}}
                <div class="bg-white rounded-lg shadow p-4 mb-6">
                    <h3 class="font-semibold mb-3">Laisser un avis</h3>
                    <form action="{{ route('review.store', $shop->slug) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input type="text" name="customer_name" placeholder="Votre nom" required class="w-full border-gray-300 rounded-md text-sm mb-2">
                            <div class="flex items-center gap-1 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" onclick="setRating({{ $i }})" class="text-2xl text-gray-300 hover:text-yellow-400 star-btn">★</button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="rating-input" value="5">
                            <textarea name="comment" rows="2" placeholder="Votre avis..." class="w-full border-gray-300 rounded-md text-sm"></textarea>
                        </div>
                        <button type="submit" class="bg-emerald-500 text-white px-4 py-2 rounded-lg text-sm">Publier</button>
                    </form>
                </div>

                {{-- Liste des avis --}}
                @if($reviews->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($reviews as $review)
                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="flex items-start justify-between">
                                    <div><p class="font-medium">{{ $review->customer_name }}</p><div class="flex items-center mt-1">@for($i=1;$i<=5;$i++)<i class="fas fa-star text-sm {{ $i<=$review->rating?'text-yellow-400':'text-gray-300' }}"></i>@endfor</div></div>
                                    <span class="text-xs text-gray-400">{{ $review->created_at->format('d/m/Y') }}</span>
                                </div>
                                @if($review->comment)<p class="text-gray-600 mt-2">{{ $review->comment }}</p>@endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12"><i class="far fa-comment-dots text-6xl text-gray-300 mb-4"></i><p class="text-gray-500">Aucun avis pour le moment.</p></div>
                @endif
            </div>

            <script>
                function setRating(r) {
                    document.getElementById('rating-input').value = r;
                    document.querySelectorAll('.star-btn').forEach((btn, i) => {
                        btn.classList.toggle('text-yellow-400', i < r);
                        btn.classList.toggle('text-gray-300', i >= r);
                    });
                }
            </script>

            <div x-show="currentTab === 'info'" x-cloak>
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Informations</h2>
                    <div class="space-y-4">
                        @if($shop->address)<div class="flex items-start"><i class="fas fa-map-marker-alt text-emerald-500 mt-1 w-6"></i><div><p class="text-sm text-gray-500">Adresse</p><p>{{ $shop->address }}</p></div></div>@endif
                        <div class="flex items-start"><i class="fab fa-whatsapp text-green-500 mt-1 w-6"></i><div><p class="text-sm text-gray-500">WhatsApp</p><a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $shop->whatsapp_phone) }}" target="_blank" class="text-emerald-600 hover:underline">{{ $shop->whatsapp_phone }}</a></div></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barre navigation mobile -->
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg z-30 md:hidden">
            <div class="flex">
                <button @click="currentTab = 'menu'" :class="currentTab === 'menu' ? 'text-emerald-600' : 'text-gray-400'" class="flex-1 py-3 text-center"><i class="fas fa-utensils text-lg"></i><span class="block text-xs mt-1">Menu</span></button>
                <button @click="currentTab = 'reviews'" :class="currentTab === 'reviews' ? 'text-emerald-600' : 'text-gray-400'" class="flex-1 py-3 text-center"><i class="fas fa-star text-lg"></i><span class="block text-xs mt-1">Avis</span></button>
                <button @click="currentTab = 'info'" :class="currentTab === 'info' ? 'text-emerald-600' : 'text-gray-400'" class="flex-1 py-3 text-center"><i class="fas fa-info-circle text-lg"></i><span class="block text-xs mt-1">Infos</span></button>
            </div>
        </div>

        <!-- Bouton panier -->
        <div x-show="cartCount > 0" class="fixed bottom-20 right-4 z-20 md:bottom-8" @click="window.location.href='{{ route('storefront.checkout', $shop->slug) }}'">
            <button class="bg-emerald-500 text-white rounded-full p-4 shadow-lg relative"><i class="fas fa-shopping-cart text-xl"></i><span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center" x-text="cartCount"></span></button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function shopApp() {
            return {
                currentTab: 'menu',
                activeCategory: 'all',
                cartCount: 0,
                init() { this.loadCart(); },
                async loadCart() {
                    try { const r = await fetch(CART_GET_URL); const d = await r.json(); this.cartCount = d.count || 0; } catch(e) {}
                }
            }
        }
    </script>
@endpush
