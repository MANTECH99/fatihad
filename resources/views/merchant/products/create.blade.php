{{-- resources/views/merchant/products/create.blade.php --}}
@extends('merchant.layouts.app')

@section('title', 'Ajouter un produit')
@section('header', 'Ajouter un produit - ' . $shop->name)

@section('content')
    <div class="max-w-2xl mx-auto">
        <form action="{{ route('merchant.products.store', $shop) }}" method="POST" enctype="multipart/form-data" x-data="productForm()">
            @csrf

            <div class="bg-white rounded-lg shadow p-6 space-y-6">
                <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom du produit *</label>
                    <input type="text" name="name" id="name" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                           value="{{ old('name') }}" placeholder="ex: Thieb bou dien">
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

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                              placeholder="Décrivez le produit...">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Prix (FCFA) *</label>
                        <input type="number" name="price" id="price" required min="0" step="100"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                               value="{{ old('price') }}" placeholder="2500">
                        @error('price')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-1">Prix promo (optionnel)</label>
                        <input type="number" name="sale_price" id="sale_price" min="0" step="100"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                               value="{{ old('sale_price') }}" placeholder="2000">
                        <p class="text-xs text-gray-500 mt-1">Laissez vide si pas de promotion</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                        <select name="category_id" id="category_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Aucune</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU (optionnel)</label>
                        <input type="text" name="sku" id="sku"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                               value="{{ old('sku') }}" placeholder="REF-001">
                    </div>
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Image principale</label>
                    <input type="file" name="image" id="image" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700">
                    @error('image')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Galerie d'images</label>
                    <input type="file" name="gallery[]" multiple accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700">
                    <p class="text-xs text-gray-500 mt-1">Vous pourrez ajouter plus d'images après</p>
                </div>

                <!-- Options dynamiques -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-sm font-medium text-gray-700">Options du produit</label>
                        <button type="button" @click="addOption" class="text-sm text-emerald-600 hover:text-emerald-800">
                            <i class="fas fa-plus mr-1"></i> Ajouter une option
                        </button>
                    </div>

                    <p class="text-xs text-gray-500 mb-3">Ex: Taille (Petit, Grand), Supplément (Fromage, Viande)</p>

                    <template x-for="(option, index) in options" :key="index">
                        <div class="border rounded-md p-4 mb-3">
                            <div class="flex justify-between mb-3">
                                <h4 class="font-medium" x-text="'Option ' + (index + 1)"></h4>
                                <button type="button" @click="removeOption(index)" class="text-red-500 hover:text-red-700 text-sm">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Nom de l'option</label>
                                    <input type="text" :name="'options[name][' + index + ']'" required
                                           class="w-full border-gray-300 rounded-md text-sm" placeholder="Taille">
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Valeurs (séparées par des virgules)</label>
                                    <input type="text" :name="'options[values][' + index + ']'" required
                                           class="w-full border-gray-300 rounded-md text-sm" placeholder="Petit, Moyen, Grand">
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Suppléments de prix (séparés par des virgules, 0 si gratuit)</label>
                                    <input type="text" :name="'options[prices][' + index + ']'"
                                           class="w-full border-gray-300 rounded-md text-sm" placeholder="0, 500, 1000">
                                </div>

                                <label class="flex items-center text-sm">
                                    <input type="checkbox" :name="'options[required][' + index + ']'" value="1" class="rounded border-gray-300 text-emerald-500">
                                    <span class="ml-2">Obligatoire</span>
                                </label>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="border-t pt-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Gestion du stock</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Quantité en stock</label>
                            <input type="number" name="stock" value="0" min="0" class="w-full border-gray-300 rounded-md text-sm">
                        </div>

                        {{-- AJOUT : Seuil d'alerte --}}
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Seuil d'alerte (rupture)</label>
                            <input type="number" name="stock_alert" value="5" min="0" class="w-full border-gray-300 rounded-md text-sm">
                            <p class="text-xs text-gray-500 mt-1">Sera notifié si le stock descend sous ce nombre</p>
                        </div>

                        {{-- AJOUT : Fournisseur --}}
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Fournisseur</label>
                            <input type="text" name="supplier" class="w-full border-gray-300 rounded-md text-sm" placeholder="Nom du fournisseur">
                        </div>

                        <div class="flex items-end pb-2 col-span-1 md:col-span-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="track_inventory" value="1" class="rounded border-gray-300 text-emerald-500">
                                <span class="ml-2 text-sm">Suivre le stock</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_available" value="1" checked class="rounded border-gray-300 text-emerald-500">
                        <span class="ml-2 text-sm">Produit disponible</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1" class="rounded border-gray-300 text-emerald-500">
                        <span class="ml-2 text-sm">Mettre en avant</span>
                    </label>
                </div>
            </div>


            {{-- Section Facebook --}}
            <div class="border-t pt-4 mt-4">
                <h3 class="text-sm font-medium text-gray-700 mb-3">📢 Publication Facebook</h3>

                @if($shop->hasFacebookConnected())
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fab fa-facebook text-blue-600 text-xl mr-3"></i>
                                <div>
                                    <p class="font-medium text-blue-900">
                                        Page connectée : {{ $shop->facebook_page_name }}
                                    </p>
                                    <a href="{{ $shop->facebook_page_url }}" target="_blank"
                                       class="text-sm text-blue-600 hover:underline">
                                        Voir la page
                                    </a>
                                </div>
                            </div>
                            {{-- ➡️ Bouton déconnexion ici --}}
                            {{-- Remplacer le <form> par un <a> --}}
                            <a href="#" onclick="event.preventDefault(); document.getElementById('fb-disconnect-form').submit();"
                               class="text-red-500 text-sm hover:text-red-700 whitespace-nowrap">
                                <i class="fas fa-unlink mr-1"></i> Déconnecter
                            </a>
                        </div>
                    </div>

                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="publish_to_facebook" value="1"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm font-medium">
                Publier ce produit sur ma page Facebook
            </span>
                    </label>
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                        <p class="text-sm text-gray-600 mb-3">
                            Connectez votre page Facebook pour publier automatiquement vos produits.
                        </p>
                        {{-- Dans create.blade.php --}}
                        <a href="{{ route('merchant.facebook.connect', ['shop_id' => $shop->id]) }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                            <i class="fab fa-facebook mr-2"></i>
                            Connecter ma page Facebook
                        </a>
                    </div>
                @endif
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('merchant.products.index', $shop) }}" class="px-4 py-2 border rounded-md hover:bg-gray-100">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2 bg-emerald-500 text-white rounded-md hover:bg-emerald-600">
                    <i class="fas fa-save mr-2"></i> Ajouter le produit
                </button>
            </div>
        </form>
    </div>

    {{-- Formulaire de déconnexion Facebook CACHÉ, hors du formulaire produit --}}
    <form id="fb-disconnect-form" action="{{ route('merchant.facebook.disconnect') }}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="shop_id" value="{{ $shop->id }}">
    </form>
@endsection

@push('scripts')
    <script>
        function productForm() {
            return {
                options: [],
                addOption() {
                    this.options.push({});
                },
                removeOption(index) {
                    this.options.splice(index, 1);
                }
            }
        }
    </script>
@endpush
