@extends('merchant.layouts.app')

@section('title', 'Retargeting')
@section('header', '🎯 Retargeting - Relancez vos visiteurs')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
            <h3 class="font-bold text-purple-800">💡 Comment ça marche ?</h3>
            <p class="text-sm text-purple-600 mt-1">
                Facebook montrera automatiquement vos produits aux personnes qui ont visité votre site sans acheter.
                Chaque visiteur verra le produit qu'il a consulté.
            </p>
        </div>

        <form action="{{ route('merchant.boost.retargeting', $shop) }}" method="POST">
            @csrf

            <div class="bg-white rounded-lg shadow p-6 space-y-6">
                <!-- Budget -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">💰 Budget quotidien</label>
                    <div class="grid grid-cols-3 gap-3">
                        @php $budgets = [3 => '3€/jour', 5 => '5€/jour', 10 => '10€/jour']; @endphp
                        @foreach($budgets as $value => $label)
                            <label class="border rounded-lg p-3 text-center cursor-pointer hover:border-purple-500">
                                <input type="radio" name="daily_budget" value="{{ $value }}" class="hidden budget-radio" {{ $value == 5 ? 'checked' : '' }}>
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

                <!-- Récapitulatif -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium mb-2">📊 Récapitulatif</h4>
                    <div id="recap" class="text-sm space-y-1">
                        <p>Type : <strong>Retargeting automatique</strong></p>
                        <p>Cible : <strong>Visiteurs des 30 derniers jours</strong></p>
                        <p>Budget : <strong id="recap_budget">-</strong></p>
                        <p>Durée : <strong id="recap_duration">-</strong></p>
                        <p class="text-lg font-bold text-purple-600">Total estimé : <span id="recap_total">-</span>€</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('merchant.boost.index', $shop) }}" class="px-4 py-2 border rounded-md hover:bg-gray-100">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2 bg-purple-500 text-white rounded-md hover:bg-purple-600">
                    🎯 Lancer le retargeting
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
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
