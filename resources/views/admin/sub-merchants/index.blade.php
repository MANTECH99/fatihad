@extends('layouts.superadmin')

@section('content')
    <div class="max-w-5xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold" style="color: #4D1111">🏪 Sous-marchands</h1>
            <a href="{{ route('admin.sub-merchants.create') }}"
               class="text-white px-4 py-2 rounded-lg font-bold"
               style="background-color: #E81E25">
                + Nouveau
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead style="background-color: #f8f9fa; border-bottom: 2px solid #E81E25;">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase" style="color: #4D1111;">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase" style="color: #4D1111;">Nom</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase" style="color: #4D1111;">Nom commercial</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase" style="color: #4D1111;">Site</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase" style="color: #4D1111;">Statut</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase" style="color: #4D1111;">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($subMerchants as $sm)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 text-xs font-mono">{{ $sm->sub_merchant_id }}</td>
                        <td class="px-4 py-3 font-medium">{{ $sm->name }}</td>
                        <td class="px-4 py-3">{{ $sm->commercial_name }}</td>
                        <td class="px-4 py-3">
                            @if($sm->site === 'disso')
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Disso</span>
                            @elseif($sm->site === 'caravane')
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Caravane</span>
                            @elseif($sm->site === 'Seneshop')
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Seneshop</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $sm->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $sm->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.sub-merchants.edit', $sm) }}" class="text-blue-600 hover:underline text-xs">✏️ Modifier</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
