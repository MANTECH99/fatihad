@extends('merchant.layouts.app')

@section('title', 'Campagne WhatsApp')
@section('header', '📱 Campagne Click-to-WhatsApp')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <h3 class="font-bold text-green-800">💡 Comment ça marche ?</h3>
            <p class="text-sm text-green-600 mt-1">
                Votre pub Facebook affichera un bouton <strong>"Envoyer un message WhatsApp"</strong>.
                Les clients cliquent et ouvrent directement une conversation WhatsApp avec vous.
            </p>
        </div>

        <form action="{{ route('merchant.boost.whatsapp', $shop) }}" enctype="multipart/form-data" method="POST" x-data="{ budget: 3, duration: 7 } ">
            @csrf

            <div class="bg-white rounded-lg shadow p-6 space-y-6">
                <!-- Message -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">💬 Message pré-rempli</label>
                    <textarea name="message" rows="3" maxlength="200"
                              class="w-full border-gray-300 rounded-md"
                              placeholder="Bonjour, je voudrais commander !">Bonjour, je voudrais commander !</textarea>
                    <p class="text-xs text-gray-500 mt-1">Le client verra ce message dans WhatsApp.</p>
                </div>

                <!-- Image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">🖼️ Image de la pub</label>
                    <input type="file" name="whatsapp_image" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-green-50 file:text-green-700">
                    <p class="text-xs text-gray-500 mt-1">Format carré recommandé (1:1). Max 2 Mo.</p>
                </div>

                <!-- WhatsApp -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">📞 Numéro WhatsApp</label>
                    <input type="text" value="{{ $shop->whatsapp_phone }}" disabled
                           class="w-full border-gray-300 rounded-md bg-gray-50 text-gray-500">
                    <p class="text-xs text-gray-500 mt-1">Numéro de la boutique.</p>
                </div>

                <!-- Budget -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">💰 Budget quotidien</label>
                    <div class="grid grid-cols-4 gap-3">
                        @foreach([1 => '1€', 3 => '3€', 5 => '5€', 10 => '10€'] as $value => $label)
                            <label class="border-2 rounded-lg p-3 text-center cursor-pointer transition-all"
                                   :class="budget == {{ $value }} ? 'border-green-500 bg-green-50 shadow-md' : 'border-gray-200 hover:border-green-300'"
                                   @click="budget = {{ $value }}">
                                <input type="radio" name="daily_budget" value="{{ $value }}" class="sr-only" {{ $value == 3 ? 'checked' : '' }}>
                                <span class="font-bold text-sm">{{ $label }}</span>
                                <span class="block text-xs text-gray-500">/jour</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Durée -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">📅 Durée</label>
                    <select name="duration_days" class="w-full border-gray-300 rounded-md" x-model="duration">
                        <option value="3">3 jours</option>
                        <option value="7" selected>7 jours</option>
                        <option value="14">14 jours</option>
                        <option value="30">30 jours</option>
                    </select>
                </div>

                <!-- Récapitulatif -->
                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <h4 class="font-medium text-green-800 mb-2">📊 Récapitulatif</h4>
                    <div class="text-sm space-y-1 text-green-700">
                        <p>Budget quotidien : <strong x-text="budget + '€'"></strong></p>
                        <p>Durée : <strong x-text="duration + ' jours'"></strong></p>
                        <p class="text-lg font-bold text-green-900 mt-2">
                            Total estimé : <strong x-text="(budget * duration) + '€'"></strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('merchant.boost.index', $shop) }}" class="px-4 py-2 border rounded-md">Annuler</a>
                <button type="submit" class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                    📱 Lancer la campagne WhatsApp
                </button>
            </div>
        </form>
    </div>
@endsection
