{{-- resources/views/merchant/products/edit.blade.php --}}
@extends('merchant.layouts.app')

@section('title', 'Modifier - ' . $product->name)
@section('header', 'Modifier ' . $product->name)

@section('content')
    <div class="max-w-2xl mx-auto">
        <form id="product-edit-form" action="{{ route('merchant.products.update', ['shop' => $shop, 'product' => $product]) }}"
              method="POST" enctype="multipart/form-data" x-data="productEditForm()">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-lg shadow p-6 space-y-6">

                <div class="grid grid-cols-2 gap-4">
                <!-- Nom -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du produit *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-1">Prix d'achat (FCFA)</label>
                    <input type="number" name="cost_price" id="cost_price" min="0" step="100"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                           value="{{ old('cost_price', $product->cost_price ?? '') }}" placeholder="1500">
                    <p class="text-xs text-gray-500 mt-1">Pour calculer le bénéfice</p>
                </div>
                </div>
                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">{{ old('description', $product->description) }}</textarea>
                </div>

                <!-- Prix -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prix (FCFA) *</label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" required min="0" step="100"
                               class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prix promo (FCFA)</label>
                        <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" min="0" step="100"
                               class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <!-- Catégorie -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                    <select name="category_id" class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Aucune</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Image principale -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image principale</label>
                    <div class="flex items-center space-x-4 mb-3">
                        <img id="image-preview" src="{{ $product->image_url ?? 'https://placehold.co/200x200?text=Image' }}"
                             class="w-24 h-24 object-cover rounded-lg border">
                        @if($product->image_url)
                            <span class="text-sm text-gray-500">Image actuelle</span>
                        @endif
                    </div>
                    <input type="file" name="image" accept="image/*" onchange="previewImage(this, 'image-preview')"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-emerald-50 file:text-emerald-700">
                </div>

                <!-- Galerie -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Galerie d'images</label>

                    @if($product->gallery && count($product->gallery) > 0)
                        <div class="grid grid-cols-4 gap-2 mb-3">
                            @foreach($product->gallery as $index => $image)
                                <div class="relative">
                                    <img src="{{ asset('storage/' . $image) }}" class="w-full h-20 object-cover rounded">
                                    <button type="button"
                                            onclick="removeGalleryImage({{ $index }})"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <input type="file" name="gallery[]" multiple accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-emerald-50 file:text-emerald-700">
                </div>

                <!-- Options -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-sm font-medium text-gray-700">Options du produit</label>
                        <button type="button" @click="addOption" class="text-sm text-emerald-600 hover:text-emerald-800">
                            <i class="fas fa-plus mr-1"></i> Ajouter
                        </button>
                    </div>

                    <template x-for="(option, index) in options" :key="index">
                        <div class="border rounded-md p-4 mb-3">
                            <div class="flex justify-between mb-2">
                                <h4 class="font-medium text-sm" x-text="'Option ' + (index + 1)"></h4>
                                <button type="button" @click="removeOption(index)" class="text-red-500 text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="space-y-2">
                                <input type="text" :name="'options[name][' + index + ']'"
                                       x-model="option.name" placeholder="Nom (ex: Taille)"
                                       class="w-full border-gray-300 rounded-md text-sm">
                                <input type="text" :name="'options[values][' + index + ']'"
                                       x-model="option.values" placeholder="Valeurs (séparées par des virgules)"
                                       class="w-full border-gray-300 rounded-md text-sm">
                                <input type="text" :name="'options[prices][' + index + ']'"
                                       x-model="option.prices" placeholder="Prix supplémentaires (séparés par des virgules)"
                                       class="w-full border-gray-300 rounded-md text-sm">
                            </div>
                        </div>
                    </template>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" class="w-full border-gray-300 rounded-md text-sm">
                    </div>

                    {{-- AJOUT : Seuil d'alerte --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Seuil d'alerte</label>
                        <input type="number" name="stock_alert" value="{{ old('stock_alert', $product->stock_alert) }}" min="0" class="w-full border-gray-300 rounded-md text-sm">
                    </div>

                    {{-- AJOUT : Fournisseur --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fournisseur</label>
                        <input type="text" name="supplier" value="{{ old('supplier', $product->supplier) }}" class="w-full border-gray-300 rounded-md text-sm">
                    </div>

                    <div class="flex items-center mt-6">
                        <input type="checkbox" name="track_inventory" value="1" {{ old('track_inventory', $product->track_inventory) ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-500">
                        <span class="ml-2 text-sm">Suivre le stock</span>
                    </div>
                </div>

                <!-- Statuts -->
                <div class="flex items-center space-x-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_available" value="1" {{ old('is_available', $product->is_available) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                        <span class="ml-2 text-sm">Disponible</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                        <span class="ml-2 text-sm">Produit mis en avant</span>
                    </label>
                </div>
            </div>
        </form>

        {{-- Boutons EN DEHORS du formulaire principal --}}
        <div class="mt-6 flex justify-between">
            <form id="delete-form" action="{{ route('merchant.products.destroy', ['shop' => $shop, 'product' => $product]) }}" method="POST"
                  onsubmit="return confirm('Supprimer ce produit ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-md text-sm">
                    <i class="fas fa-trash mr-1"></i> Supprimer
                </button>
            </form>

            <div class="space-x-3">
                <a href="{{ route('merchant.products.index', $shop) }}" class="px-4 py-2 border rounded-md hover:bg-gray-100 text-sm">
                    Annuler
                </a>
                <button type="button" onclick="document.getElementById('product-edit-form').submit()" class="px-6 py-2 bg-emerald-500 text-white rounded-md hover:bg-emerald-600 text-sm">
                    <i class="fas fa-save mr-1"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function productEditForm() {
            return {
                options: @json($product->options ?? []),

                addOption() {
                    this.options.push({ name: '', values: '', prices: '' });
                },

                removeOption(index) {
                    this.options.splice(index, 1);
                }
            }
        }

        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => preview.src = e.target.result;
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeGalleryImage(index) {
            if (confirm('Supprimer cette image ?')) {
                fetch('{{ route('merchant.products.gallery.remove', ['shop' => $shop, 'product' => $product, 'index' => '__INDEX__']) }}'.replace('__INDEX__', index), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                }).then(() => location.reload());
            }
        }
    </script>
@endpush
