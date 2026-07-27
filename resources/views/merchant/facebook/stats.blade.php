@extends('merchant.layouts.app')

@section('title', 'Stats Facebook - ' . $shop->name)
@section('header', '📊 Statistiques Facebook - ' . $shop->name)

@section('content')
    <div class="space-y-6">
        {{-- Stats de la page --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-lg mb-4">📄 Page {{ $pageStats['name'] }}</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-3xl font-bold text-blue-700">{{ number_format($pageStats['followers'], 0, ',', ' ') }}</p>
                    <p class="text-sm text-gray-500 mt-1">👥 Abonnés</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-3xl font-bold text-green-700">{{ number_format($pageStats['engagement'], 0, ',', ' ') }}</p>
                    <p class="text-sm text-gray-500 mt-1">❤️ Engagements</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <p class="text-3xl font-bold text-yellow-700">{{ number_format($pageStats['new_likes'], 0, ',', ' ') }}</p>
                    <p class="text-sm text-gray-500 mt-1">👍 Nouveaux likes</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <p class="text-3xl font-bold text-purple-700">{{ $products->count() }}</p>
                    <p class="text-sm text-gray-500 mt-1">📦 Produits publiés</p>
                </div>
            </div>
        </div>

        {{-- Stats par produit --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-lg mb-4">📦 Performances des produits</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                    <tr class="border-b text-left text-sm text-gray-500">
                        <th class="pb-3">Produit</th>
                        <th class="pb-3 text-center">👍 Likes</th>
                        <th class="pb-3 text-center">💬 Com.</th>
                        <th class="pb-3 text-center">🔄 Partages</th>
                        <th class="pb-3 text-center">👁️ Impressions</th>
                        <th class="pb-3 text-center">👥 Engagés</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $product)
                        <tr class="border-b">
                            <td class="py-3 flex items-center gap-2">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" class="w-8 h-8 rounded object-cover">
                                @endif
                                <span class="text-sm font-medium">{{ $product->name }}</span>
                            </td>
                            <td class="py-3 text-center text-sm">{{ $product->fb_stats['likes'] }}</td>
                            <td class="py-3 text-center text-sm">{{ $product->fb_stats['comments'] }}</td>
                            <td class="py-3 text-center text-sm">{{ $product->fb_stats['shares'] }}</td>
                            <td class="py-3 text-center text-sm">{{ $product->fb_stats['impressions'] }}</td>
                            <td class="py-3 text-center text-sm">{{ $product->fb_stats['engaged_users'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
