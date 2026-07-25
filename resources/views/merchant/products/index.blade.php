{{-- resources/views/merchant/products/index.blade.php --}}
@extends('merchant.layouts.app')

@section('title', 'Produits - ' . $shop->name)
@section('header', 'Produits - ' . $shop->name)

@section('content')
    <div x-data="productManager()">
        <div class="flex justify-between items-center mb-6 -mt-4">
            <div class="flex space-x-2">
                <a href="{{ route('merchant.categories.index', $shop) }}" class="bg-gray-500 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-600">
                    <i class="fas fa-tags mr-2"></i> Nos Catégories
                </a>

            </div>
            <a href="{{ route('merchant.products.create', $shop) }}" class="bg-emerald-500 text-white px-4 py-2 rounded-md text-sm hover:bg-emerald-600">
                <i class="fas fa-plus mr-2"></i> Ajouter un produit
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        <!-- Stats produits -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-6">
            @php
                $allProducts = $products ?? collect();
                $activeProducts = $allProducts->where('is_available', true)->filter(function($p) {
                    return !$p->track_inventory || $p->stock > 0;
                });
                $inactiveCount = $allProducts->where('is_available', false)->count()
                               + $allProducts->where('is_available', true)->where('track_inventory', true)->where('stock', '<=', 0)->count();
                $totalStock = $allProducts->sum('stock');
                $totalValue = $allProducts->sum(function($p) {
                    if($p->stock <= 0) return 0;
                    $price = $p->hasDiscount() ? $p->sale_price : $p->price;
                    return $price * $p->stock;
                });
            @endphp

            {{-- Valeur Stock (CA potentiel) --}}
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex items-center justify-between shadow-sm lg:shadow-none">
                <div>
                    <p class="text-sm text-gray-500">Valeur Stock</p>
                    <p class="mt-1 text-sm lg:text-2xl font-bold text-gray-900">{{ number_format($totalValue, 0, ',', ' ') }}</p>
                    <p class="text-xs text-gray-400 mt-1">CA potentiel</p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-full bg-emerald-100 flex items-center justify-center">
                    <i class="fas fa-coins text-emerald-600 text-base lg:text-xl"></i>
                </div>
            </div>

            {{-- Catalogue --}}
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex items-center justify-between shadow-sm lg:shadow-none">
                <div>
                    <p class="text-sm text-gray-500">Catalogue</p>
                    <p class="mt-1 text-base lg:text-2xl font-bold text-gray-900">{{ $allProducts->count() }}</p>
                    <p class="text-xs text-gray-400 mt-1">produits</p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-boxes text-blue-600 text-base lg:text-xl"></i>
                </div>
            </div>

            {{-- Disponible --}}
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex items-center justify-between shadow-sm lg:shadow-none">
                <div>
                    <p class="text-sm text-gray-500">Disponible</p>
                    <p class="mt-1 text-base lg:text-2xl font-bold text-green-600">{{ $activeProducts->count() }}</p>
                    <p class="text-xs text-gray-400 mt-1">Actifs et en stock</p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-base lg:text-xl"></i>
                </div>
            </div>

            {{-- Indisponible --}}
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex items-center justify-between shadow-sm lg:shadow-none">
                <div>
                    <p class="text-sm text-gray-500">Indisponible</p>
                    <p class="mt-1 text-base lg:text-2xl font-bold text-red-600">{{ $inactiveCount }}</p>
                    <p class="text-xs text-gray-400 mt-1">Rupture de stock</p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fas fa-ban text-red-600 text-base lg:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">

            @if($products->isNotEmpty())
                <!-- Version mobile : cartes -->
                <div class="md:hidden divide-y divide-gray-200">
                    @foreach($products as $product)
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-12 h-12 rounded object-cover mr-3">
                                    @endif
                                    <div>
                                        <p class="font-medium">{{ $product->name }}</p>
                                        @if($product->sku)
                                            <p class="text-xs text-gray-500">SKU: {{ $product->sku }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500">{{ $product->category?->name ?? '-' }}</p>
                                    </div>
                                </div>
                                <button @click="toggleAvailability({{ $product->id }})"
                                        class="px-2 py-1 text-xs rounded-full cursor-pointer"
                                        :class="getStatusClass({{ $product->is_available ? 'true' : 'false' }})">
                                    {{ $product->is_available ? 'Disponible' : 'Indisponible' }}
                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-sm mt-2">
                                <div>
                                    <p class="text-gray-500 text-xs">Fournisseur</p>
                                    <p>{{ $product->supplier ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs">Stock</p>
                                    @if($product->track_inventory)
                                        <span class="{{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $product->stock }}
                                </span>
                                    @else
                                        <span class="text-gray-400">∞</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs">Prix d'achat</p>
                                    <p>{{ $product->cost_price ? number_format($product->cost_price, 0, ',', ' ') . ' FCFA' : '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs">Prix de vente</p>
                                    @if($product->hasDiscount())
                                        <span class="font-medium text-emerald-600">{{ number_format($product->sale_price, 0, ',', ' ') }} FCFA</span>
                                        <br>
                                        <span class="text-xs text-gray-400 line-through">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                                    @else
                                        <span class="font-medium">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3 mt-3 pt-3 border-t border-gray-100">
                                <a href="{{ route('merchant.products.edit', ['shop' => $shop, 'product' => $product]) }}"
                                   class="text-blue-600 hover:text-blue-800 text-sm inline-flex items-center">
                                    <i class="fas fa-edit mr-1"></i> Modifier
                                </a>
                                <form action="{{ route('merchant.products.destroy', ['shop' => $shop, 'product' => $product]) }}"
                                      method="POST" class="inline" onsubmit="return confirm('Supprimer ce produit ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm inline-flex items-center">
                                        <i class="fas fa-trash mr-1"></i> Supprimer
                                    </button>
                                </form>
                                @if($product->facebook_post_id)
                                    <a href="{{ route('merchant.boost.create', ['shop' => $shop, 'product' => $product]) }}"
                                       class="text-orange-600 hover:text-orange-800 text-sm inline-flex items-center" title="Booster">
                                        <i class="fas fa-rocket mr-1"></i> Booster
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Version desktop : tableau -->
                <div class="hidden md:block">
                <table class="w-full">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fournisseur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix d'achat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix de vente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @foreach($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-10 h-10 rounded object-cover mr-3">
                                    @endif
                                    <div>
                                        <p class="font-medium">{{ $product->name }}</p>
                                        @if($product->sku)
                                            <p class="text-xs text-gray-500">SKU: {{ $product->sku }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ $product->category?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ $product->supplier ?? '-' }}
                            </td>

                            <td class="px-6 py-4 text-sm">
                                {{ $product->cost_price ? number_format($product->cost_price, 0, ',', ' ') . ' FCFA' : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($product->hasDiscount())
                                    <span class="font-medium text-emerald-600">{{ number_format($product->sale_price, 0, ',', ' ') }} FCFA</span>
                                    <br>
                                    <span class="text-xs text-gray-400 line-through">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                                @else
                                    <span class="font-medium">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <button @click="toggleAvailability({{ $product->id }})"
                                        class="px-2 py-1 text-xs rounded-full cursor-pointer"
                                        :class="getStatusClass({{ $product->is_available ? 'true' : 'false' }})">
                                    {{ $product->is_available ? 'Disponible' : 'Indisponible' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($product->track_inventory)
                                    <span class="{{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ $product->stock }}
        </span>
                                @else
                                    <span class="text-gray-400">∞</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm space-x-2">
                                <a href="{{ route('merchant.products.edit', ['shop' => $shop, 'product' => $product]) }}"
                                   class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('merchant.products.destroy', ['shop' => $shop, 'product' => $product]) }}"
                                      method="POST" class="inline" onsubmit="return confirm('Supprimer ce produit ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @if($product->facebook_post_id)
                                    <a href="{{ route('merchant.boost.create', ['shop' => $shop, 'product' => $product]) }}"
                                       class="text-orange-600 hover:text-orange-800" title="Booster">
                                        <i class="fas fa-rocket"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>

                <div class="px-6 py-4">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 mb-4">Aucun produit pour le moment.</p>
                    <a href="{{ route('merchant.products.create', $shop) }}" class="text-emerald-600 hover:text-emerald-800">
                        <i class="fas fa-plus mr-2"></i> Ajouter votre premier produit
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function productManager() {
            return {
                async toggleAvailability(productId) {
                    try {
                        const response = await fetch(`{{ route('merchant.products.toggle', ['shop' => $shop, 'product' => '__ID__']) }}`.replace('__ID__', productId), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            location.reload();
                        }
                    } catch (error) {
                        alert('Erreur lors du changement de statut');
                    }
                },
                getStatusClass(isAvailable) {
                    return isAvailable
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800';
                }
            }
        }
    </script>
@endpush
