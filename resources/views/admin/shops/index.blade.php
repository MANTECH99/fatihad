{{-- resources/views/admin/shops/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Boutiques')
@section('header', 'Gestion des boutiques')

@section('content')
    <div class="bg-white rounded-lg shadow">
        <!-- Filtres -->
        <div class="p-4 border-b">
            <form action="{{ route('admin.shops.index') }}" method="GET" class="flex flex-wrap gap-3">
                <input type="text" name="search" placeholder="Rechercher..." value="{{ request('search') }}"
                       class="border-gray-300 rounded-md text-sm">
                <select name="status" class="border-gray-300 rounded-md text-sm">
                    <option value="">Tous</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvées</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejetées</option>
                </select>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm">Filtrer</button>
            </form>
        </div>

        <!-- Tableau -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Boutique</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Propriétaire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commandes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @forelse($shops as $shop)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <img src="{{ $shop->logo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($shop->name) . '&background=10b981&color=fff' }}"
                                     class="w-10 h-10 rounded-full mr-3">
                                <div>
                                    <p class="font-medium">{{ $shop->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $shop->city ?? 'Ville non spécifiée' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium">{{ $shop->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $shop->user->email }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($shop->status === 'approved')
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Approuvée</span>
                            @elseif($shop->status === 'pending')
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Rejetée</span>
                            @endif

                            @if(!$shop->is_active)
                                <span class="ml-1 px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $shop->orders_count }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $shop->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.shops.show', $shop) }}"
                               class="text-indigo-600 hover:text-indigo-800" title="Voir détails">
                                <i class="fas fa-eye"></i>
                            </a>

                            @if($shop->status === 'pending')
                                <form action="{{ route('admin.shops.approve', $shop) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800" title="Approuver">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.shops.reject', $shop) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Rejeter">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('admin.shops.toggle-active', $shop) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-yellow-600 hover:text-yellow-800" title="{{ $shop->is_active ? 'Désactiver' : 'Activer' }}">
                                    <i class="fas fa-{{ $shop->is_active ? 'ban' : 'check-circle' }}"></i>
                                </button>
                            </form>

                            <form action="{{ route('admin.shops.destroy', $shop) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Supprimer cette boutique définitivement ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-store text-4xl mb-3 block"></i>
                            Aucune boutique trouvée
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($shops->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $shops->links() }}
            </div>
        @endif
    </div>
@endsection
