@extends('merchant.layouts.app')

@section('title', 'Modifier la campagne')
@section('header', '✏️ Modifier la campagne')

@section('content')
    <div class="max-w-2xl mx-auto">
        <form action="{{ route('merchant.boost.update', ['shop' => $shop, 'campaign' => $campaign]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-lg shadow p-6 space-y-6">
                @if($campaign->campaign_type === 'boost')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Produit</label>
                        <select name="product_id" class="w-full border-gray-300 rounded-md">
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ $campaign->product_id == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">💰 Budget quotidien (€)</label>
                    <input type="number" name="daily_budget" value="{{ $campaign->daily_budget }}" min="1" max="1000" step="0.5"
                           class="w-32 border-gray-300 rounded-md">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">📅 Durée (jours)</label>
                    <select name="duration_days" class="w-full border-gray-300 rounded-md">
                        @foreach([3, 7, 14, 30] as $days)
                            <option value="{{ $days }}" {{ $campaign->duration_days == $days ? 'selected' : '' }}>
                                {{ $days }} jours
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('merchant.boost.index', $shop) }}" class="px-4 py-2 border rounded-md">Annuler</a>
                <button type="submit" class="px-6 py-2 bg-emerald-500 text-white rounded-md">💾 Enregistrer</button>
            </div>
        </form>
    </div>
@endsection
