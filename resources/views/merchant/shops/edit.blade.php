{{-- resources/views/merchant/shops/edit.blade.php --}}
@extends('merchant.layouts.app')

@section('title', 'Modifier - ' . $shop->name)
@section('header', 'Modifier ' . $shop->name)

@section('content')
    <div x-data="shopEditor()" class="max-w-4xl mx-auto">
        <!-- Onglets -->
        <div class="flex space-x-1 bg-white rounded-lg shadow mb-6 p-1">
            <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'bg-emerald-500 text-white' : 'text-gray-600 hover:bg-gray-100'"
                    class="flex-1 py-3 px-4 rounded-md text-sm font-medium transition">
                <i class="fas fa-info-circle mr-2"></i> Général
            </button>
            <button @click="activeTab = 'images'" :class="activeTab === 'images' ? 'bg-emerald-500 text-white' : 'text-gray-600 hover:bg-gray-100'"
                    class="flex-1 py-3 px-4 rounded-md text-sm font-medium transition">
                <i class="fas fa-images mr-2"></i> Images
            </button>
            <button @click="activeTab = 'horaires'" :class="activeTab === 'horaires' ? 'bg-emerald-500 text-white' : 'text-gray-600 hover:bg-gray-100'"
                    class="flex-1 py-3 px-4 rounded-md text-sm font-medium transition">
                <i class="fas fa-clock mr-2"></i> Horaires
            </button>
            <button @click="activeTab = 'preview'" :class="activeTab === 'preview' ? 'bg-emerald-500 text-white' : 'text-gray-600 hover:bg-gray-100'"
                    class="flex-1 py-3 px-4 rounded-md text-sm font-medium transition">
                <i class="fas fa-eye mr-2"></i> Aperçu
            </button>
        </div>

        <form action="{{ route('merchant.shops.update', $shop) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <!-- Onglet Général -->
            <div x-show="activeTab === 'general'" class="bg-white rounded-lg shadow p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la boutique *</label>
                        <input type="text" name="name" value="{{ old('name', $shop->name) }}" required
                               class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                        @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                        <input type="text" name="city" value="{{ old('city', $shop->city) }}"
                               class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="ex: Dakar, Thiès, Saint-Louis">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                              placeholder="Décrivez votre boutique, votre spécialité...">{{ old('description', $shop->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp principal *</label>
                        <input type="text" name="whatsapp_phone" value="{{ old('whatsapp_phone', $shop->whatsapp_phone) }}" required
                               class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="+221 77 123 45 67">
                        @error('whatsapp_phone')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone secondaire</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', $shop->contact_phone) }}"
                               class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Numéro Wave (paiements)</label>
                        <input type="text" name="wave_number" value="{{ old('wave_number', $shop->wave_number) }}"
                               class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="77 123 45 67">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Numéro Orange Money (paiements)</label>
                        <input type="text" name="orange_money_number" value="{{ old('orange_money_number', $shop->orange_money_number) }}"
                               class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="77 123 45 67">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Méthode de réception des paiements</label>
                    <select name="payout_method" class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="wave" {{ ($shop->payout_method ?? 'wave') === 'wave' ? 'selected' : '' }}>Wave</option>
                        <option value="orange_money" {{ ($shop->payout_method ?? 'wave') === 'orange_money' ? 'selected' : '' }}>Orange Money</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email de contact</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email', $shop->contact_email) }}"
                               class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                        <input type="text" name="address" value="{{ old('address', $shop->address) }}"
                               class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                {{-- Zones de livraison avec prix --}}
                <div x-data="deliveryZones()" class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700">Zones de livraison avec prix</label>

                    <template x-for="(zone, index) in zones" :key="index">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <input type="text" :name="'delivery_zones['+index+'][name]'" x-model="zone.name" required
                                   class="flex-1 min-w-0 w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="Nom de la zone (ex: Yoff)">
                            <input type="number" :name="'delivery_zones['+index+'][price]'" x-model="zone.price" min="0" step="100"
                                   class="w-36 border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
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


                {{-- ID du compte publicitaire Facebook --}}
                <div class="border-t pt-4 mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        📢 ID du compte publicitaire Facebook
                    </label>
                    <input type="text" name="facebook_ad_account_id"
                           value="{{ old('facebook_ad_account_id', $shop->facebook_ad_account_id) }}"
                           class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="Ex: 123456789">
                    <p class="text-xs text-gray-500 mt-1">
                        Pour les campagnes de boost. Trouvable dans le Gestionnaire de publicités Facebook.
                    </p>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        📦 ID du catalogue Facebook
                    </label>
                    <input type="text" name="facebook_catalog_id"
                           value="{{ old('facebook_catalog_id', $shop->facebook_catalog_id) }}"
                           class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="Ex: 123456789">
                    <p class="text-xs text-gray-500 mt-1">
                        Pour synchroniser vos produits avec Facebook Shop.
                        <a href="https://business.facebook.com/commerce" target="_blank" class="text-emerald-600 underline">
                            Créer un catalogue
                        </a>
                    </p>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        📊 ID du Pixel Meta
                    </label>
                    <input type="text" name="facebook_pixel_id"
                           value="{{ old('facebook_pixel_id', $shop->facebook_pixel_id) }}"
                           class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="Ex: 123456789012345">
                    <p class="text-xs text-gray-500 mt-1">
                        Pour le suivi des visites et le retargeting.
                        <a href="https://business.facebook.com/settings/pixels/" target="_blank" class="text-emerald-600 underline">
                            Créer un Pixel
                        </a>
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Frais par défaut (FCFA)</label>
                        <input type="number" name="delivery_fee" value="{{ old('delivery_fee', $shop->delivery_fee) }}" min="0" step="100"
                               class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                        <p class="text-xs text-gray-500 mt-1">Si aucune zone sélectionnée</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Commande minimum (FCFA)</label>
                        <input type="number" name="min_order" value="{{ old('min_order', $shop->min_order) }}" min="0"
                               class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">État de la boutique</label>
                        <div class="mt-2 flex items-center space-x-4">
                            <button type="button" @click="toggleShopStatus()"
                                    :class="isOpen ? 'bg-green-500' : 'bg-gray-300'"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                <span :class="isOpen ? 'translate-x-6' : 'translate-x-1'"
                      class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                            </button>
                            <input type="hidden" name="is_open" :value="isOpen ? '1' : '0'">
                            <span class="text-sm" x-text="isOpen ? 'Ouverte' : 'Fermée'"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Images -->
            <div x-show="activeTab === 'images'" x-cloak class="bg-white rounded-lg shadow p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Logo</label>
                        <div class="flex items-center justify-center mb-4">
                            <div class="w-32 h-32 rounded-full border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden bg-gray-50">
                                <img id="logo-preview" src="{{ $shop->logo_url ?? 'https://placehold.co/200x200?text=Logo' }}"
                                     class="w-full h-full object-cover" alt="Logo">
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <input type="file" name="logo" accept="image/*" onchange="previewImage(this, 'logo-preview')"
                                   class="flex-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700">
                            @if($shop->logo)
                                <button type="button" onclick="document.getElementById('delete-logo').value='1'; document.getElementById('logo-preview').src='https://placehold.co/200x200?text=Logo'"
                                        class="px-3 py-2 bg-red-500 text-white rounded-md text-sm hover:bg-red-600 shrink-0">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                        <input type="hidden" name="delete_logo" id="delete-logo" value="0">
                        <p class="text-xs text-gray-500 mt-2">Carré recommandé. Max 2 Mo. Format: JPG, PNG, WebP</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Image de couverture</label>
                        <div class="mb-4 rounded-lg border-2 border-dashed border-gray-300 overflow-hidden bg-gray-50 h-40">
                            <img id="cover-preview" src="{{ $shop->cover_image_url ?? 'https://placehold.co/1200x400?text=Couverture' }}"
                                 class="w-full h-full object-cover" alt="Couverture">
                        </div>
                        <div class="flex gap-2">
                            <input type="file" name="cover_image" accept="image/*" onchange="previewImage(this, 'cover-preview')"
                                   class="flex-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700">
                            @if($shop->cover_image)
                                <button type="button" onclick="document.getElementById('delete-cover').value='1'; document.getElementById('cover-preview').src='https://placehold.co/1200x400?text=Couverture'"
                                        class="px-3 py-2 bg-red-500 text-white rounded-md text-sm hover:bg-red-600 shrink-0">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                        <input type="hidden" name="delete_cover" id="delete-cover" value="0">
                        <p class="text-xs text-gray-500 mt-2">Paysage recommandé (1200×400). Max 5 Mo.</p>
                    </div>

                    <!-- --- AJOUTE LE CACHET ICI (EN DESSOUS DE LA GRILLE) --- -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Cachet / Sceau</label>
                        <div class="flex items-center justify-center mb-4">
                            <div class="w-32 h-32 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden bg-gray-50">
                                <img id="stamp-preview" src="{{ $shop->stamp_url ?? 'https://placehold.co/200x200?text=Cachet' }}"
                                     class="w-full h-full object-contain" alt="Cachet">
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <input type="file" name="stamp" accept="image/*" onchange="previewImage(this, 'stamp-preview')"
                                   class="flex-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700">
                            @if($shop->stamp)
                                <button type="button" onclick="document.getElementById('delete-stamp').value='1'; document.getElementById('stamp-preview').src='https://placehold.co/200x200?text=Cachet'"
                                        class="px-3 py-2 bg-red-500 text-white rounded-md text-sm hover:bg-red-600 shrink-0">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                        <input type="hidden" name="delete_stamp" id="delete-stamp" value="0">
                        <p class="text-xs text-gray-500 mt-2">Fond transparent recommandé (PNG). Max 2 Mo.</p>
                    </div>
                    <!-- --- FIN AJOUT --- -->
                </div>
            </div>

            <!-- Onglet Horaires -->
            <div x-show="activeTab === 'horaires'" x-cloak class="bg-white rounded-lg shadow p-6">
                @php
                    $days = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
                    $hours = $shop->opening_hours ?? [];
                @endphp

                @foreach($days as $index => $day)
                    <div class="flex items-center py-3 {{ !$loop->last ? 'border-b' : '' }} gap-1 sm:gap-3">
                        {{-- Jour --}}
                        <div class="w-20 sm:w-24 shrink-0">
                            <span class="text-[10px] sm:text-sm font-medium capitalize">{{ $day }}</span>
                        </div>

                        {{-- Horaires --}}
                        <div class="flex-1 flex items-center gap-0.5 sm:gap-3 min-w-0">
                            <input type="time" name="opening_hours[{{ $day }}][open]"
                                   value="{{ $hours[$day]['open'] ?? '08:00' }}"
                                   class="w-16 sm:w-auto flex-1 min-w-0 border-gray-300 rounded-md text-[10px] sm:text-sm p-1 sm:p-2">
                            <span class="text-gray-500 text-[10px] sm:text-sm shrink-0">à</span>
                            <input type="time" name="opening_hours[{{ $day }}][close]"
                                   value="{{ $hours[$day]['close'] ?? '22:00' }}"
                                   class="w-16 sm:w-auto flex-1 min-w-0 border-gray-300 rounded-md text-[10px] sm:text-sm p-1 sm:p-2">
                        </div>

                        {{-- Fermé --}}
                        <label class="flex items-center ml-0.5 sm:ml-4 shrink-0">
                            <input type="checkbox" name="opening_hours[{{ $day }}][closed]" value="1"
                                   {{ isset($hours[$day]['closed']) && $hours[$day]['closed'] ? 'checked' : '' }}
                                   class="w-3 h-3 sm:w-4 sm:h-4 rounded border-gray-300 text-red-500 focus:ring-red-500">
                            <span class="ml-0.5 sm:ml-2 text-[10px] sm:text-sm text-red-600 shrink-0">Fermé</span>
                        </label>
                    </div>
                @endforeach
            </div>

            <!-- Onglet Aperçu -->
            <div x-show="activeTab === 'preview'" x-cloak class="bg-white rounded-lg shadow p-6">
                <div class="text-center">
                    <p class="text-gray-600 mb-4">Voici à quoi ressemble votre boutique :</p>
                    <a href="{{ route('storefront.show', $shop->slug) }}" target="_blank"
                       class="inline-flex items-center px-6 py-3 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition">
                        <i class="fas fa-external-link-alt mr-2"></i> Voir la boutique en direct
                    </a>
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Lien à partager :</p>
                        {{-- Version mobile : lien tronqué --}}
                        <p class="font-mono text-sm mt-1 md:hidden truncate max-w-full">
                            {{ route('storefront.show', $shop->slug) }}
                        </p>

                        {{-- Version desktop : lien complet --}}
                        <p class="font-mono text-sm mt-1 hidden md:block break-all">
                            {{ route('storefront.show', $shop->slug) }}
                        </p>
                        <button onclick="navigator.clipboard.writeText('{{ route('storefront.show', $shop->slug) }}')"
                                class="mt-2 text-sm text-emerald-600 hover:text-emerald-800">
                            <i class="far fa-copy mr-1"></i> Copier le lien
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="history.back()" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit" class="px-6 py-2 bg-emerald-500 text-white rounded-md hover:bg-emerald-600">
                    <i class="fas fa-save mr-2"></i> Enregistrer les modifications
                </button>
            </div>
        </form>

        <!-- Section danger -->
        <div class="mt-8 bg-white rounded-lg shadow p-6 border border-red-200">
            <h3 class="text-red-600 font-semibold mb-2">Zone dangereuse</h3>
            <p class="text-sm text-gray-600 mb-4">Une fois supprimée, toutes les données de cette boutique seront définitivement effacées.</p>
            <form action="{{ route('merchant.shops.destroy', $shop) }}" method="POST"
                  onsubmit="return confirm('Êtes-vous ABSOLUMENT sûr de vouloir supprimer cette boutique ? Cette action est IRRÉVERSIBLE.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm">
                    <i class="fas fa-trash mr-2"></i> Supprimer cette boutique
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function shopEditor() {
            return {
                activeTab: 'general',
                isOpen: {{ $shop->is_open ? 'true' : 'false' }},

                toggleShopStatus() {
                    this.isOpen = !this.isOpen;

                    // Sauvegarde automatique via AJAX
                    fetch('{{ route('merchant.shops.toggle-status', $shop) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    });
                }
            }
        }

        function deliveryZones() {
            return {
                zones: @json($shop->delivery_zones ?? []),

                addZone() {
                    this.zones.push({ name: '', price: 0 });
                },

                removeZone(index) {
                    this.zones.splice(index, 1);
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
    </script>
@endpush
