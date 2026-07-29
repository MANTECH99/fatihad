{{-- resources/views/storefront/partials/product-card.blade.php --}}
@props(['product', 'shop'])

<a href="{{ route('storefront.product', ['shop' => $shop->slug, 'product' => $product->slug]) }}" class="bg-white rounded-lg shadow-sm hover:shadow-md transition block border border-gray-200">

    <div class="relative w-full h-40 bg-white rounded-t-lg overflow-hidden flex items-center justify-center p-2">
        @if($product->image_url)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="max-width:100%;max-height:100%;object-fit:contain;" loading="lazy">
        @else
            <i class="fas fa-image text-4xl text-gray-400"></i>
        @endif
        @if($product->hasDiscount())
            <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">-{{ $product->discount_percentage }}%</span>
        @endif

            {{-- ★★★ AJOUT DU CŒUR EN HAUT À DROITE ★★★ --}}
            <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm rounded-full w-8 h-8 flex items-center justify-center shadow-sm text-gray-400 hover:text-red-500 transition cursor-pointer z-10">
                <i class="fas fa-heart text-sm"></i>
            </div>
    </div>

    <div class="p-3">
        <h3 class="font-medium text-gray-900 truncate">{{ $product->name }}</h3>
        @if($product->category)
            <p class="text-xs text-gray-400 mt-0.5">{{ $product->category->name }}</p>
        @endif

        {{-- Stock + Avis --}}
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
            <span class="text-emerald-500">                    <i class="fas fa-shopping-cart text-sm"></i></span>
        </div>
    </div>
</a>
