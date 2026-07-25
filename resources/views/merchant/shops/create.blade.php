{{-- resources/views/merchant/shops/create.blade.php --}}
@extends('merchant.layouts.app')

@section('title', 'Créer une boutique')
@section('header', 'Créer une boutique')

@section('content')
    <div class="max-w-2xl mx-auto">
        <form action="{{ route('merchant.shops.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow">
            @csrf

            <div class="p-6 space-y-6">
                <h2 class="text-lg font-semibold border-b pb-2">Informations de base</h2>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom de la boutique *</label>
                    <input type="text" name="name" id="name" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                           value="{{ old('name') }}" placeholder="ex: Chez Fatou">
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                              placeholder="Décrivez votre boutique...">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="whatsapp_phone" class="block text-sm font-medium text-gray-700 mb-1">WhatsApp *</label>
                        <input type="text" name="whatsapp_phone" id="whatsapp_phone" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                               value="{{ old('whatsapp_phone', auth()->user()->phone) }}" placeholder="+221 77 123 45 67">
                        @error('whatsapp_phone')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone secondaire</label>
                        <input type="text" name="contact_phone" id="contact_phone"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                               value="{{ old('contact_phone') }}" placeholder="+221 70 123 45 67">
                    </div>
                </div>

                <h2 class="text-lg font-semibold border-b pb-2">Paiements & Retraits</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="wave_number" class="block text-sm font-medium text-gray-700 mb-1">Numéro Wave (retraits)</label>
                        <input type="text" name="wave_number" id="wave_number"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                               value="{{ old('wave_number') }}" placeholder="77 123 45 67">
                        <p class="text-xs text-gray-500 mt-1">Numéro pour recevoir les paiements Wave</p>
                    </div>
                    <div>
                        <label for="orange_money_number" class="block text-sm font-medium text-gray-700 mb-1">Numéro Orange Money (retraits)</label>
                        <input type="text" name="orange_money_number" id="orange_money_number"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                               value="{{ old('orange_money_number') }}" placeholder="77 123 45 67">
                        <p class="text-xs text-gray-500 mt-1">Numéro pour recevoir les paiements Orange Money</p>
                    </div>
                </div>

                <div class="mt-4">
                    <label for="payout_method" class="block text-sm font-medium text-gray-700 mb-1">Méthode de réception des paiements</label>
                    <select name="payout_method" id="payout_method"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="wave" {{ old('payout_method', 'wave') == 'wave' ? 'selected' : '' }}>Wave</option>
                        <option value="orange_money" {{ old('payout_method') == 'orange_money' ? 'selected' : '' }}>Orange Money</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Choisissez sur quel réseau recevoir vos revenus</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="contact_email" id="contact_email"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                               value="{{ old('contact_email', auth()->user()->email) }}" placeholder="contact@boutique.com">
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                        <input type="text" name="city" id="city"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                               value="{{ old('city') }}" placeholder="ex: Dakar">
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                    <textarea name="address" id="address" rows="2"
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                              placeholder="Adresse complète">{{ old('address') }}</textarea>
                </div>

                <div x-data="deliveryZonesCreate()" class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700">Zones de livraison avec prix</label>

                    <template x-for="(zone, index) in zones" :key="index">
                        <div class="flex items-center gap-3">
                            <input type="text" :name="'delivery_zones['+index+'][name]'" x-model="zone.name" required
                                   class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="Nom de la zone (ex: Yoff)">
                            <input type="number" :name="'delivery_zones['+index+'][price]'" x-model="zone.price" min="0" step="100"
                                   class="w-36 border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="Prix (FCFA)">
                            <button type="button" @click="removeZone(index)" class="text-red-500 hover:text-red-700 shrink-0">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </template>

                    <button type="button" @click="addZone()" class="text-sm text-emerald-600 hover:text-emerald-800">
                        <i class="fas fa-plus mr-1"></i> Ajouter une zone
                    </button>
                </div>

                <h2 class="text-lg font-semibold border-b pb-2">Images</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                        <input type="file" name="logo" id="logo" accept="image/*"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        <p class="text-xs text-gray-500 mt-1">Carré, max 2 Mo</p>
                    </div>
                    <div>
                        <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-1">Image de couverture</label>
                        <input type="file" name="cover_image" id="cover_image" accept="image/*"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        <p class="text-xs text-gray-500 mt-1">Paysage, max 5 Mo</p>
                    </div>
                </div>

                <!-- --- AJOUTE LE CACHET ICI --- -->
                <div class="mt-4">
                    <label for="stamp" class="block text-sm font-medium text-gray-700 mb-1">Cachet / Sceau</label>
                    <input type="file" name="stamp" id="stamp" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    <p class="text-xs text-gray-500 mt-1">Fond transparent recommandé (PNG). Max 2 Mo.</p>
                </div>

                <h2 class="text-lg font-semibold border-b pb-2">Paramètres</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="delivery_fee" class="block text-sm font-medium text-gray-700 mb-1">Frais par défaut (FCFA)</label>
                        <input type="number" name="delivery_fee" id="delivery_fee" min="0"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                               value="{{ old('delivery_fee', 0) }}" placeholder="1000">
                        <p class="text-xs text-gray-500 mt-1">Si aucune zone sélectionnée</p>
                    </div>
                    <div>
                        <label for="min_order" class="block text-sm font-medium text-gray-700 mb-1">Commande minimum (FCFA)</label>
                        <input type="number" name="min_order" id="min_order" min="0"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                               value="{{ old('min_order', 0) }}" placeholder="3000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">État</label>
                        <label class="flex items-center mt-2">
                            <input type="checkbox" name="is_open" value="1" {{ old('is_open', true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-emerald-500 shadow-sm focus:ring-emerald-500">
                            <span class="ml-2 text-sm">Boutique ouverte</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                <a href="{{ route('merchant.dashboard') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2 bg-emerald-500 text-white rounded-md hover:bg-emerald-600">
                    <i class="fas fa-save mr-2"></i> Créer la boutique
                </button>
            </div>
        </form>
    </div>
    <script>
        function deliveryZonesCreate() {
            return {
                zones: [],

                addZone() {
                    this.zones.push({ name: '', price: 0 });
                },

                removeZone(index) {
                    this.zones.splice(index, 1);
                }
            }
        }
    </script>
@endsection
