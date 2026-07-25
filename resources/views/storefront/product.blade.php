{{-- resources/views/storefront/product.blade.php --}}
@extends('layouts.storefront')

@section('title', $product->name . ' - ' . $shop->name)

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        /* Styles communs */
        .product-gallery .swiper-slide,
        .product-gallery-desktop .swiper-slide {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #f3f4f6;
        }
        .product-gallery .swiper-wrapper,
        .product-gallery-desktop .swiper-wrapper {
            align-items: center;
        }
        .product-gallery img,
        .product-gallery-desktop img {
            max-width: 100%;
            width: auto !important;
            height: auto !important;
            object-fit: contain !important;
        }
        .swiper-pagination-bullet-active {
            background: #10b981 !important;
        }
        .miniature-img {
            border: 2px solid transparent;
            cursor: pointer;
        }
        .miniature-img.active {
            border-color: #10b981;
        }

        /* Mobile (par défaut) */
        .product-gallery .swiper-slide {
            height: 350px !important;
        }
        .product-gallery img {
            max-height: 330px;
        }

        /* Desktop */
        @media (min-width: 768px) {
            .mobile-only {
                display: none !important;
            }
            .desktop-only {
                display: block !important;
            }
            .product-gallery-desktop .swiper-slide {
                height: 400px !important;
            }
            .product-gallery-desktop img {
                max-height: 380px;
            }
            .miniature-img {
                transition: all 0.3s ease;
            }
            .miniature-img:hover {
                border-color: #10b981;
                transform: scale(1.05);
            }
            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }
            .scrollbar-hide {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        }

        @media (max-width: 767px) {
            .mobile-only {
                display: block !important;
            }
            .desktop-only {
                display: none !important;
            }
        }
    </style>
@endpush

@section('content')

    @if($shop->facebook_pixel_id)
        <script>
            fbq('track', 'ViewContent', {
                content_ids: ['{{ $product->id }}'],
                content_type: 'product',
                content_name: '{{ addslashes($product->name) }}',
                content_category: '{{ addslashes($product->category->name ?? '') }}',
                value: {{ $product->current_price }},
                currency: 'XOF'
            });
        </script>
    @endif

    @php
        $allImages = [];
        if($product->image_url) $allImages[] = $product->image_url;
        if($product->gallery) {
            foreach($product->gallery as $image) {
                $allImages[] = $image;
            }
        }

        $relatedProducts = \App\Models\Product::where('shop_id', $shop->id)
            ->where('id', '!=', $product->id)
            ->where('is_available', true)
            ->inRandomOrder()
            ->take(4)
            ->get();
    @endphp

    {{-- ==================== VERSION MOBILE ==================== --}}
    <div class="mobile-only max-w-lg mx-auto px-4 py-6" x-data="productPage({{ $product->id }})">
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-6 overflow-x-auto scrollbar-hide whitespace-nowrap">
            <a href="{{ route('storefront.show', $shop->slug) }}" class="hover:text-emerald-600 transition flex-shrink-0">
                <i class="fas fa-store mr-1"></i> {{ $shop->name }}
            </a>
            <span class="flex-shrink-0">›</span>
            <span class="text-gray-800 font-medium flex-shrink-0">{{ $product->name }}</span>
        </nav>

        {{-- Galerie Swiper Mobile --}}
        <div class="swiper product-gallery rounded-xl overflow-hidden mb-4">
            <div class="swiper-wrapper">
                @foreach($allImages as $image)
                    <div class="swiper-slide">
                        <img src="{{ Str::startsWith($image, 'http') ? $image : asset('storage/' . $image) }}" alt="{{ $product->name }}">
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>

        @if(count($allImages) > 1)
            <div class="flex gap-2 mb-4 overflow-x-auto">
                @foreach($allImages as $index => $image)
                    <div class="w-16 h-24 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 cursor-pointer miniature-img {{ $index === 0 ? 'active' : '' }}">
                        <img src="{{ Str::startsWith($image, 'http') ? $image : asset('storage/' . $image) }}"
                             onclick="galleryMobile.slideTo({{ $index }})"
                             class="max-w-full max-h-full object-contain"
                             data-index="{{ $index }}">
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Badge Mobile --}}
        <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-medium">
            <i class="fas fa-store mr-1"></i> {{ $shop->name }}
        </span>

        <h1 class="text-xl font-bold mt-2">{{ $product->name }}</h1>
        @if($product->description)<p class="text-gray-500 mt-1">{{ $product->description }}</p>@endif

        <div class="mt-4 text-2xl font-bold text-emerald-600">
            <span x-text="formatPrice(currentPrice)"></span>
            @if($product->hasDiscount())
                <span class="text-sm text-gray-400 line-through ml-2">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
            @endif
        </div>

        @if($product->options)
            <div class="mt-4 space-y-3">
                @foreach($product->options as $index => $option)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $option['name'] }}</label>
                        <select @change="updateOption({{ $index }}, $event.target.value)" class="w-full border-gray-300 rounded-md">
                            @foreach($option['values'] as $i => $value)
                                <option value="{{ $value }}">{{ $value }} {{ ($option['prices'][$i] ?? 0) > 0 ? '(+'.number_format($option['prices'][$i], 0, ',', ' ').' F)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Selectionnez une quantité</label>
            <div class="flex items-center space-x-3">
                <button @click="if(quantity > 1) quantity--" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">-</button>
                <span class="text-lg font-medium w-8 text-center" x-text="quantity">1</span>
                <button @click="if(quantity < 99) quantity++" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">+</button>
            </div>
        </div>

        {{-- Notes des avis --}}
        <div class="mt-4 flex items-center space-x-2">
            <div class="flex text-yellow-400">
                @php
                    $avgRating = $product->reviews()->where('is_approved', true)->avg('rating') ?? 0;
                    $reviewCount = $product->reviews()->where('is_approved', true)->count();
                @endphp
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= round($avgRating))
                        ★
                    @else
                        ☆
                    @endif
                @endfor
            </div>
            <span class="text-sm text-gray-600">({{ $reviewCount }} avis)</span>
        </div>

        {{-- Stock --}}
        <div class="mt-2">
            @if($product->track_inventory && $product->stock > 0)
                <span class="text-green-600 text-sm">✅ En stock ({{ $product->stock }} disponibles)</span>
            @elseif(!$product->track_inventory)
                <span class="text-green-600 text-sm">✅ En stock</span>
            @else
                <span class="text-red-600 text-sm">❌ Rupture de stock</span>
            @endif
        </div>

        <div class="mt-6 space-y-3">
            @if($product->track_inventory && $product->stock <= 0)
                <button disabled class="w-full bg-gray-300 text-gray-500 py-3 rounded-xl font-bold cursor-not-allowed">
                    ❌ Rupture de stock
                </button>
            @else
                <button @click="addToCart()" id="addToCartBtn" class="w-full bg-emerald-500 text-white py-3 rounded-xl font-bold">
                    <i class="fas fa-cart-plus mr-2"></i> Ajouter au panier - <span x-text="formatPrice(totalPrice())"></span>
                </button>
                <button @click="buyNow()" class="w-full border-2 border-emerald-500 text-emerald-600 py-3 rounded-xl font-bold">
                    <i class="fas fa-bolt mr-2"></i> Commander maintenant
                </button>
            @endif
        </div>

        {{-- Aide et Partage Desktop --}}
        <div class="mt-8 border-t pt-6 space-y-4">

            <!-- Promotions -->
            <div>
                <h3 class="font-bold text-md mb-2">🎁 Promotions</h3>
                <p class="text-sm text-gray-600">
                    Prépayez avec Orange Money ou Wave dès 10 000 FCFA et bénéficiez de la livraison gratuite, en point relais, jusqu'à 5 000 FCFA offerts.
                </p>
            </div>

            <div>
                <h3 class="font-bold text-sm mb-2">📞 Besoin d'aide pour commander ?</h3>
                <p class="text-sm text-gray-600">
                    Contactez {{ $shop->name }} au
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $shop->whatsapp_phone) }}"
                       target="_blank"
                       class="font-bold text-emerald-600 hover:underline">
                        {{ $shop->whatsapp_phone }}
                    </a>
                </p>
            </div>

            <div>
                <h3 class="font-bold text-sm mb-2">📤 Partagez ce produit</h3>
                <div class="flex items-center space-x-3">
                    <a href="https://wa.me/?text={{ urlencode($product->name . ' - ' . route('storefront.product', ['shop' => $shop->slug, 'product' => $product->id])) }}"
                       target="_blank"
                       class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center hover:bg-green-600 transition">
                        <i class="fab fa-whatsapp text-lg"></i>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('storefront.product', ['shop' => $shop->slug, 'product' => $product->id])) }}"
                       target="_blank"
                       class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition">
                        <i class="fab fa-facebook-f text-lg"></i>
                    </a>

                    <!-- Twitter / X -->
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode(route('storefront.product', ['shop' => $shop->slug, 'product' => $product->id])) }}"
                       target="_blank"
                       class="w-10 h-10 bg-black text-white rounded-full flex items-center justify-center hover:bg-gray-800 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>

                    <!-- Instagram -->
                    <a href="https://www.instagram.com/"
                       target="_blank"
                       class="w-10 h-10 bg-gradient-to-br from-purple-500 via-pink-500 to-orange-400 text-white rounded-full flex items-center justify-center hover:from-purple-600 hover:via-pink-600 hover:to-orange-500 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t text-center">
            <p class="text-sm text-gray-500">
                <i class="fab fa-whatsapp text-green-500 mr-1"></i> Besoin d'aide ?
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $shop->whatsapp_phone) }}" target="_blank" class="text-emerald-600 font-medium hover:underline">
                    Contactez {{ $shop->name }}
                </a>
            </p>
        </div>


        {{-- Avis sur le produit --}}
        <div class="mt-8 border-t pt-6">
            <h2 class="text-lg font-bold mb-4">Avis sur ce produit</h2>

            <form action="{{ route('review.store', $shop->slug) }}" method="POST" class="bg-white rounded-lg shadow p-4 mb-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="text" name="customer_name" placeholder="Votre nom" required class="w-full border-gray-300 rounded-md text-sm mb-2">
                <div class="flex items-center gap-1 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" onclick="setRatingMobile({{ $i }})" class="text-2xl text-gray-300 hover:text-yellow-400 star-btn">★</button>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="rating-input" value="5">
                <textarea name="comment" rows="2" placeholder="Votre avis..." class="w-full border-gray-300 rounded-md text-sm mb-2"></textarea>
                <button type="submit" class="bg-emerald-500 text-white px-4 py-2 rounded-lg text-sm">Publier</button>
            </form>

            @php $productReviews = \App\Models\Review::where('shop_id', $shop->id)->where('product_id', $product->id)->latest()->take(5)->get(); @endphp
            @foreach($productReviews as $review)
                <div class="bg-white rounded-lg shadow p-3 mb-2">
                    <p class="font-medium text-sm">{{ $review->customer_name }}</p>
                    <div class="text-yellow-400 text-sm">@for($i=1;$i<=5;$i++){{ $i<=$review->rating?'★':'☆' }}@endfor</div>
                    @if($review->comment)<p class="text-gray-500 text-sm mt-1">{{ $review->comment }}</p>@endif
                </div>
            @endforeach
        </div>
        {{-- Produits similaires Mobile --}}
        @if($relatedProducts->count() > 0)
            <div class="mt-8 border-t pt-6">
                <h2 class="text-lg font-bold mb-4">Produits similaires</h2>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($relatedProducts as $related)
                        @include('storefront.partials.product-card', ['product' => $related, 'shop' => $shop])
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- ==================== VERSION DESKTOP ==================== --}}
    <div class="desktop-only max-w-7xl mx-auto px-4 py-6" x-data="productPage({{ $product->id }})" style="display: none;">
        {{-- Fil d'Ariane --}}
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-6 overflow-x-auto scrollbar-hide whitespace-nowrap">
            <a href="{{ route('storefront.show', $shop->slug) }}" class="hover:text-emerald-600 transition flex-shrink-0">
                <i class="fas fa-store mr-1"></i> {{ $shop->name }}
            </a>
            <span class="flex-shrink-0">›</span>
            <span class="text-gray-800 font-medium flex-shrink-0">{{ $product->name }}</span>
        </nav>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="grid grid-cols-2 gap-8">
                {{-- Galerie Desktop --}}
                <div>
                    <div class="swiper product-gallery-desktop rounded-lg overflow-hidden mb-4 relative bg-gray-100">
                        <div class="swiper-wrapper">
                            @foreach($allImages as $image)
                                <div class="swiper-slide">
                                    <img src="{{ Str::startsWith($image, 'http') ? $image : asset('storage/' . $image) }}" alt="{{ $product->name }}">
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>

                    @if(count($allImages) > 1)
                        <div class="flex justify-center gap-2 mt-4">
                            @foreach($allImages as $index => $image)
                                <div class="w-20 h-24 bg-gray-100 rounded flex items-center justify-center cursor-pointer miniature-img {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ Str::startsWith($image, 'http') ? $image : asset('storage/' . $image) }}"
                                         onclick="galleryDesktop.slideTo({{ $index }})"
                                         class="max-w-full max-h-full object-contain"
                                         data-index="{{ $index }}">
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-8 border-t pt-6">
                        <h3 class="font-semibold mb-3">Laisser un avis</h3>
                        <form action="{{ route('review.store', $shop->slug) }}" method="POST" class="bg-white rounded-lg shadow p-4">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="text" name="customer_name" placeholder="Votre nom" required class="w-full border-gray-300 rounded-md text-sm mb-2">
                            <div class="flex items-center gap-1 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" onclick="setRatingDesktop({{ $i }})" class="text-2xl text-gray-300 hover:text-yellow-400 star-btn">★</button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="rating-input-desktop" value="5">
                            <textarea name="comment" rows="2" placeholder="Votre avis..." class="w-full border-gray-300 rounded-md text-sm mb-2"></textarea>
                            <button type="submit" class="bg-emerald-500 text-white px-4 py-2 rounded-lg text-sm">Publier</button>
                        </form>
                    </div>
                </div>

                {{-- Infos produit Desktop --}}
                <div>
                    <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded text-sm font-medium">
                        <i class="fas fa-store mr-1"></i> Boutique {{ $shop->name }}
                    </span>

                    <h1 class="text-2xl font-bold mt-4">{{ $product->name }}</h1>

                    @if($product->description)
                        <p class="text-gray-500 mt-2">{{ Str::limit($product->description, 200) }}</p>
                    @endif

                    {{-- Prix Desktop --}}
                    <div class="mt-4">
                        <span class="text-3xl font-bold text-emerald-600" x-text="formatPrice(currentPrice)"></span>
                        @if($product->hasDiscount())
                            <span class="text-gray-400 line-through text-xl ml-3">
                                {{ number_format($product->price, 0, ',', ' ') }} FCFA
                            </span>
                            <span class="bg-red-500 text-white px-2 py-1 rounded text-sm ml-2">
                                -{{ $product->discount_percentage ?? 0 }}%
                            </span>
                        @endif
                    </div>

                    {{-- Notes des avis --}}
                    <div class="mt-4 flex items-center space-x-2">
                        <div class="flex text-yellow-400">
                            @php
                                $avgRating = $product->reviews()->where('is_approved', true)->avg('rating') ?? 0;
                                $reviewCount = $product->reviews()->where('is_approved', true)->count();
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($avgRating))
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                        </div>
                        <span class="text-sm text-gray-600">({{ $reviewCount }} avis)</span>
                    </div>

                    {{-- Stock --}}
                    <div class="mt-2">
                        @if($product->track_inventory && $product->stock > 0)
                            <span class="text-green-600 text-sm">✅ En stock ({{ $product->stock }} disponibles)</span>
                        @elseif(!$product->track_inventory)
                            <span class="text-green-600 text-sm">✅ En stock</span>
                        @else
                            <span class="text-red-600 text-sm">❌ Rupture de stock</span>
                        @endif
                    </div>

                    {{-- Options Desktop --}}
                    @if($product->options)
                        <div class="mt-6 space-y-4">
                            @foreach($product->options as $index => $option)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $option['name'] }}</label>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($option['values'] as $i => $value)
                                            <button @click="updateOption({{ $index }}, '{{ $value }}')"
                                                    :class="selectedOptions[{{ $index }}] === '{{ $value }}' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="px-4 py-2 rounded-lg text-sm transition">
                                                {{ $value }}
                                                @if(($option['prices'][$i] ?? 0) > 0)
                                                    <span class="text-xs ml-1">(+{{ number_format($option['prices'][$i], 0, ',', ' ') }} F)</span>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Quantité Desktop --}}
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantité</label>
                        <div class="flex items-center space-x-4">
                            <button @click="if(quantity > 1) quantity--"
                                    class="w-10 h-10 rounded-full border-2 border-gray-300 flex items-center justify-center text-xl font-bold hover:border-emerald-500 transition">
                                -
                            </button>
                            <span class="text-2xl font-bold w-12 text-center" x-text="quantity">1</span>
                            <button @click="if(quantity < 99) quantity++"
                                    class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xl font-bold hover:bg-emerald-600 transition">
                                +
                            </button>
                        </div>
                    </div>

                    {{-- Boutons Desktop --}}
                    <div class="mt-8 space-y-3">
                        @if($product->track_inventory && $product->stock <= 0)
                            <button disabled class="w-full bg-gray-300 text-gray-500 py-4 rounded-lg font-bold text-lg cursor-not-allowed">
                                ❌ Rupture de stock
                            </button>
                        @else
                            <button @click="addToCart()" id="addToCartBtnDesktop" class="w-full bg-emerald-500 text-white py-4 rounded-lg font-bold text-lg hover:bg-emerald-600 transition flex items-center justify-center space-x-2">
                                <i class="fas fa-cart-plus"></i>
                                <span>Ajouter au panier - <span x-text="formatPrice(totalPrice())"></span></span>
                            </button>
                            <button @click="buyNow()"
                                    class="w-full border-2 border-emerald-500 text-emerald-600 py-4 rounded-lg font-bold text-lg hover:bg-emerald-50 transition flex items-center justify-center space-x-2">
                                <i class="fas fa-bolt"></i>
                                <span>Commander maintenant</span>
                            </button>
                        @endif
                    </div>

                    {{-- Aide et Partage Desktop --}}
                    <div class="mt-8 border-t pt-6 space-y-4">

                        <!-- Promotions -->
                        <div>
                            <h3 class="font-bold text-md mb-2">🎁 Promotions</h3>
                            <p class="text-sm text-gray-600">
                                Prépayez avec Orange Money ou Wave dès 10 000 FCFA et bénéficiez de la livraison gratuite, en point relais, jusqu'à 5 000 FCFA offerts.
                            </p>
                        </div>

                        <div>
                            <h3 class="font-bold text-sm mb-2">📞 Besoin d'aide pour commander ?</h3>
                            <p class="text-sm text-gray-600">
                                Contactez {{ $shop->name }} au
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $shop->whatsapp_phone) }}"
                                   target="_blank"
                                   class="font-bold text-emerald-600 hover:underline">
                                    {{ $shop->whatsapp_phone }}
                                </a>
                            </p>
                        </div>

                        <div>
                            <h3 class="font-bold text-sm mb-2">📤 Partagez ce produit</h3>
                            <div class="flex items-center space-x-3">
                                <a href="https://wa.me/?text={{ urlencode($product->name . ' - ' . route('storefront.product', ['shop' => $shop->slug, 'product' => $product->id])) }}"
                                   target="_blank"
                                   class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center hover:bg-green-600 transition">
                                    <i class="fab fa-whatsapp text-lg"></i>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('storefront.product', ['shop' => $shop->slug, 'product' => $product->id])) }}"
                                   target="_blank"
                                   class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition">
                                    <i class="fab fa-facebook-f text-lg"></i>
                                </a>

                                <!-- Twitter / X -->
                                <a href="https://twitter.com/intent/tweet?text={{ urlencode(route('storefront.product', ['shop' => $shop->slug, 'product' => $product->id])) }}"
                                   target="_blank"
                                   class="w-10 h-10 bg-black text-white rounded-full flex items-center justify-center hover:bg-gray-800 transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                    </svg>
                                </a>

                                <!-- Instagram -->
                                <a href="https://www.instagram.com/"
                                   target="_blank"
                                   class="w-10 h-10 bg-gradient-to-br from-purple-500 via-pink-500 to-orange-400 text-white rounded-full flex items-center justify-center hover:from-purple-600 hover:via-pink-600 hover:to-orange-500 transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Description Desktop --}}
            @if($product->description)
                <div class="mt-8 border-t pt-6">
                    <h2 class="text-xl font-bold mb-4 px-4 py-2 inline-block w-full bg-gray-100 rounded">Description</h2>
                    <div class="prose max-w-none text-gray-700 mt-4">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            @endif

            {{-- Produits similaires Desktop --}}
            @php
                $relatedProducts = \App\Models\Product::where('shop_id', $shop->id)
                    ->where('id', '!=', $product->id)
                    ->where('is_available', true)
                    ->inRandomOrder()
                    ->take(4)
                    ->get();
            @endphp

            @if($relatedProducts->count() > 0)
                <div class="mt-8 border-t pt-6">
                    <h2 class="text-xl font-bold mb-4 px-4 py-2 inline-block w-full bg-gray-100 rounded">Produits similaires</h2>
                    <div class="grid grid-cols-4 gap-4 mt-4">
                        @foreach($relatedProducts as $related)
                            @include('storefront.partials.product-card', ['product' => $related, 'shop' => $shop])
                        @endforeach
                    </div>
                </div>
            @endif
            @php $productReviews = \App\Models\Review::where('shop_id', $shop->id)->where('product_id', $product->id)->latest()->take(5)->get(); @endphp
            @if($productReviews->count() > 0)
                <div class="mt-8 border-t pt-6">
                    <h2 class="text-lg font-bold mb-4">Avis clients</h2>
                    @foreach($productReviews as $review)
                        <div class="bg-white rounded-lg shadow p-3 mb-2">
                            <p class="font-medium text-sm">{{ $review->customer_name }}</p>
                            <div class="text-yellow-400 text-sm">@for($i=1;$i<=5;$i++){{ $i<=$review->rating?'★':'☆' }}@endfor</div>
                            @if($review->comment)<p class="text-gray-500 text-sm mt-1">{{ $review->comment }}</p>@endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection



@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>


        function setRatingMobile(r) {
            document.getElementById('rating-input').value = r;
            const form = document.getElementById('rating-input').closest('form');
            form.querySelectorAll('.star-btn').forEach((btn, i) => {
                btn.classList.toggle('text-yellow-400', i < r);
                btn.classList.toggle('text-gray-300', i >= r);
            });
        }

        function setRatingDesktop(r) {
            document.getElementById('rating-input-desktop').value = r;
            const form = document.getElementById('rating-input-desktop').closest('form');
            form.querySelectorAll('.star-btn').forEach((btn, i) => {
                btn.classList.toggle('text-yellow-400', i < r);
                btn.classList.toggle('text-gray-300', i >= r);
            });
        }
        let galleryMobile;
        let galleryDesktop;

        document.addEventListener('DOMContentLoaded', function() {
            // Swiper Mobile
// Swiper Mobile
            const swiperElMobile = document.querySelector('.product-gallery');
            if (swiperElMobile) {
                galleryMobile = new Swiper('.product-gallery', {
                    loop: false,
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true
                    },
                    on: {
                        slideChange: function() {
                            document.querySelectorAll('.mobile-only .miniature-img').forEach((img, i) => {
                                img.classList.toggle('active', i === this.activeIndex);
                            });
                        }
                    }
                });
            }

// Swiper Desktop
            const swiperElDesktop = document.querySelector('.product-gallery-desktop');
            if (swiperElDesktop) {
                galleryDesktop = new Swiper('.product-gallery-desktop', {
                    loop: false,
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true
                    },
                    on: {
                        slideChange: function() {
                            document.querySelectorAll('.desktop-only .miniature-img').forEach((img, i) => {
                                img.classList.toggle('active', i === this.activeIndex);
                            });
                        }
                    }
                });

                // Clics miniatures
                document.querySelectorAll('.desktop-only .miniature-img').forEach((img, index) => {
                    img.addEventListener('click', function() {
                        galleryDesktop.slideTo(index);
                    });
                });
            }
        });

        function productPage(productId) {
            return {
                quantity: 1,
                currentPrice: {{ $product->current_price }},
                selectedOptions: {},
                updateOption(index, value) {
                    this.selectedOptions[index] = value;
                    let price = {{ $product->current_price }};
                    @if($product->options)
                    const options = @json($product->options);
                    Object.keys(this.selectedOptions).forEach(i => {
                        const opt = options[i];
                        const vIndex = opt.values.indexOf(this.selectedOptions[i]);
                        if (vIndex >= 0 && opt.prices[vIndex]) price += parseFloat(opt.prices[vIndex]);
                    });
                    @endif
                        this.currentPrice = price;
                },
                totalPrice() {
                    return this.currentPrice * this.quantity;
                },
                formatPrice(p) {
                    return new Intl.NumberFormat('fr-FR').format(p) + ' FCFA';
                },
                async addToCart() {
                    const btn = document.getElementById('addToCartBtn') || document.getElementById('addToCartBtnDesktop');
                    const og = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Ajout en cours...';
                    btn.disabled = true;

                    const f = new FormData();
                    f.append('product_id', productId);
                    f.append('quantity', this.quantity);
                    if (Object.keys(this.selectedOptions).length) f.append('options', JSON.stringify(this.selectedOptions));

                    const resp = await fetch(CART_ADD_URL, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: f
                    });
                    const data = await resp.json();

                    btn.innerHTML = og;
                    btn.disabled = false;

                    const badge = document.getElementById('cart-badge');
                    if (badge && data.cart_count) {
                        badge.textContent = data.cart_count;
                        badge.style.display = 'flex';
                    }

                    // Afficher le modal succès
                    document.getElementById('successModal').style.display = 'flex';
                    setTimeout(() => { document.getElementById('successModal').style.display = 'none'; }, 700);

                    return data;
                },
                buyNow() {
                    window.location.href = '{{ route('storefront.checkout', $shop->slug) }}';
                },
            }
        }


    </script>



    <div id="successModal" style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; background:rgba(0,0,0,0.3);">
        <div style="background:white; border-radius:16px; padding:24px; text-align:center; animation:popIn 0.3s ease;">
            <div style="width:48px; height:48px; background:#d1fae5; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 12px;">
                <i class="fas fa-check" style="color:#10b981; font-size:20px;"></i>
            </div>
            <p style="font-weight:600; color:#065f46;">Produit ajouté !</p>
        </div>
    </div>
@endpush
