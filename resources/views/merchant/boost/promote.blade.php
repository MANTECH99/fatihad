@extends('merchant.layouts.app')

@section('title', 'Promouvoir Seneshop')
@section('header', '📢 Promouvoir votre SaaS')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg p-4 mb-6 text-white">
            <h3 class="font-bold">🚀 Promotion Boutique</h3>
            <p class="text-sm text-white/90 mt-1">
                Créez une publication sponsorisée qui redirige vers votre landing page.
            </p>
        </div>

        <form action="{{ route('merchant.boost.promote.store', $shop) }}" method="POST" enctype="multipart/form-data" x-data="{ budget: 1, duration: 7 }">
            @csrf

            <div class="bg-white rounded-lg shadow p-6 space-y-6">
                <!-- Message -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">📝 Message de la publication</label>
                    <textarea name="message" rows="6" required
                              class="w-full border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                              placeholder="🇸🇳 Créez votre boutique en ligne en 5 minutes !

✅ Catalogue illimité
✅ Commandes WhatsApp
✅ Paiements Wave & Orange Money
✅ 14 jours d'essai gratuit"></textarea>
                    @error('message')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- URL -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">🔗 URL de destination</label>
                    <input type="url" name="landing_url" required
                           value="https://seneshop.com"
                           class="w-full border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                    @error('landing_url')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">🖼️ Image (optionnel)</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                    <p class="text-xs text-gray-500 mt-1">Format carré ou paysage recommandé. Max 2 Mo.</p>
                    @error('image')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Budget & Durée -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">💰 Budget quotidien</label>
                    <div class="grid grid-cols-4 gap-3">
                        @foreach([1 => '1€/jour', 3 => '3€/jour', 5 => '5€/jour', 10 => '10€/jour'] as $value => $label)
                            <label
                                class="border-2 rounded-lg p-3 text-center cursor-pointer transition-all"
                                :class="budget == {{ $value }} ? 'border-purple-500 bg-purple-50 shadow-md' : 'border-gray-200 hover:border-purple-300'"
                                @click="budget = {{ $value }}">
                                <input type="radio" name="daily_budget" value="{{ $value }}" class="sr-only" {{ $value == 1 ? 'checked' : '' }}>
                                <span class="font-bold text-sm">{{ $value }}€</span>
                                <span class="block text-xs text-gray-500">/jour</span>
                            </label>
                        @endforeach
                    </div>
                    @error('daily_budget')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">📅 Durée</label>
                        <select name="duration_days" class="w-full border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" x-model="duration">
                            <option value="3">3 jours</option>
                            <option value="7">7 jours</option>
                            <option value="14">14 jours</option>
                            <option value="30">30 jours</option>
                        </select>
                        @error('duration_days')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Récapitulatif -->
                    <div class="bg-purple-50 rounded-lg p-4 border border-purple-200 mt-4">
                        <h4 class="font-medium text-purple-800 mb-2">📊 Récapitulatif</h4>
                        <div class="text-sm space-y-1 text-purple-700">
                            <p>Budget quotidien : <strong x-text="budget + '€'"></strong></p>
                            <p>Durée : <strong x-text="duration + ' jours'"></strong></p>
                            <p class="text-lg font-bold text-purple-900 mt-2">
                                Total estimé : <strong x-text="(budget * duration) + '€'"></strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('merchant.boost.index', $shop) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2 bg-purple-500 text-white rounded-md hover:bg-purple-600 transition shadow-lg shadow-purple-500/25">
                    🚀 Lancer la promotion
                </button>
            </div>
        </form>
    </div>
@endsection
