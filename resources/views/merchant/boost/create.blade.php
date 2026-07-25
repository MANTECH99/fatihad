{{-- resources/views/merchant/boost/create.blade.php --}}
@extends('merchant.layouts.app')

@section('title', 'Booster - ' . $product->name)
@section('header', '🚀 Booster : ' . $product->name)

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center space-x-4 mb-4">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-20 h-20 rounded-lg object-cover">
                <div>
                    <h3 class="font-bold text-lg">{{ $product->name }}</h3>
                    <p class="text-emerald-600 font-bold">{{ number_format($product->price, 0, ',', ' ') }} FCFA</p>
                </div>
            </div>
        </div>

        <form action="{{ route('merchant.boost.store', ['shop' => $shop, 'product' => $product]) }}" method="POST">
            @csrf

            <div class="bg-white rounded-lg shadow p-6 space-y-6">
                <!-- Budget -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">💰 Budget quotidien</label>
                    <div class="grid grid-cols-3 gap-3">
                        @php $budgets = [2 => '2€/jour', 5 => '5€/jour', 10 => '10€/jour']; @endphp
                        @foreach($budgets as $value => $label)
                            <label class="border rounded-lg p-3 text-center cursor-pointer hover:border-emerald-500">
                                <input type="radio" name="daily_budget" value="{{ $value }}" class="hidden budget-radio">
                                <span class="font-bold">{{ $value }}€</span>
                                <span class="block text-xs text-gray-500">/jour</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <label class="text-sm text-gray-500">Ou personnalisé :</label>
                        <input type="number" name="daily_budget" id="custom_budget" min="1" max="1000" step="0.5"
                               class="w-32 border-gray-300 rounded-md text-sm" placeholder="Montant">
                        <span class="text-sm text-gray-500">€</span>
                    </div>
                    @error('daily_budget')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Durée -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">📅 Durée</label>
                    <select name="duration_days" class="w-full border-gray-300 rounded-md">
                        <option value="3">3 jours</option>
                        <option value="7" selected>7 jours</option>
                        <option value="14">14 jours</option>
                        <option value="30">30 jours</option>
                    </select>
                    @error('duration_days')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Audience -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">🎯 Audience cible</label>
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:border-emerald-500">
                            <input type="radio" name="audience_type" value="local" checked class="mr-3">
                            <div>
                                <span class="font-medium">Autour de ma boutique</span>
                                <span class="block text-xs text-gray-500">Personnes dans un rayon de 20km</span>
                            </div>
                        </label>

                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:border-emerald-500">
                            <input type="radio" name="audience_type" value="followers" class="mr-3">
                            <div>
                                <span class="font-medium">Abonnés et leurs amis</span>
                                <span class="block text-xs text-gray-500">Personnes qui suivent votre page</span>
                            </div>
                        </label>

                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:border-emerald-500">
                            <input type="radio" name="audience_type" value="custom" class="mr-3">
                            <div>
                                <span class="font-medium">Ciblage personnalisé</span>
                                <span class="block text-xs text-gray-500">Choisissez vos critères</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Ciblage personnalisé -->
                <div id="custom_targeting" style="display:none;">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Âge minimum</label>
                            <input type="number" name="age_min" value="18" min="18" max="65" class="w-full border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Âge maximum</label>
                            <input type="number" name="age_max" value="65" min="18" max="65" class="w-full border-gray-300 rounded-md text-sm">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="block text-sm text-gray-700 mb-1">Centres d'intérêt</label>
                        <input type="text" name="interests" class="w-full border-gray-300 rounded-md text-sm" placeholder="Ex: cuisine, mode, sport">
                    </div>
                    <div class="mt-3">
                        <label class="block text-sm text-gray-700 mb-1">Ville</label>
                        <input type="text" name="city" class="w-full border-gray-300 rounded-md text-sm" placeholder="Dakar">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">📅 Date de début</label>
                    <input type="datetime-local" name="starts_at"
                           value="{{ now()->format('Y-m-d\TH:i') }}"
                           min="{{ now()->format('Y-m-d\TH:i') }}"
                           class="w-full border-gray-300 rounded-md text-sm">
                    <p class="text-xs text-gray-500 mt-1">Laissez vide pour démarrer immédiatement</p>
                </div>

                <!-- Récapitulatif -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium mb-2">📊 Récapitulatif</h4>
                    <div id="recap" class="text-sm space-y-1">
                        <p>Budget : <strong id="recap_budget">-</strong></p>
                        <p>Durée : <strong id="recap_duration">-</strong></p>
                        <p class="text-lg font-bold text-emerald-600">Total estimé : <span id="recap_total">-</span>€</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('merchant.products.index', $shop) }}" class="px-4 py-2 border rounded-md hover:bg-gray-100">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2 bg-emerald-500 text-white rounded-md hover:bg-emerald-600">
                    🚀 Lancer le boost
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Afficher/masquer ciblage personnalisé
        document.querySelectorAll('input[name="audience_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('custom_targeting').style.display =
                    this.value === 'custom' ? 'block' : 'none';
            });
        });

        // Calculer le récapitulatif
        function updateRecap() {
            const budget = document.querySelector('input[name="daily_budget"]:checked')?.value
                || document.getElementById('custom_budget').value;
            const duration = document.querySelector('select[name="duration_days"]').value;

            if (budget && duration) {
                document.getElementById('recap_budget').textContent = budget + '€/jour';
                document.getElementById('recap_duration').textContent = duration + ' jours';
                document.getElementById('recap_total').textContent = (budget * duration) + '€';
            }
        }

        document.querySelectorAll('input[name="daily_budget"], #custom_budget, select[name="duration_days"]')
            .forEach(el => el.addEventListener('change', updateRecap));
        updateRecap();
    </script>
@endpush
