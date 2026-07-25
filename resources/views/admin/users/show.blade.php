{{-- resources/views/admin/users/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Utilisateur - ' . $user->name)
@section('header', $user->name)

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center space-x-4 mb-6">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=6366f1&color=fff&size=80"
                         class="w-16 h-16 rounded-full">
                    <div>
                        <h2 class="text-xl font-bold">{{ $user->name }}</h2>
                        <p class="text-gray-500">{{ $user->email }}</p>
                    </div>
                    <div class="ml-auto">
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $user->role === 'admin' ? 'Administrateur' : 'Commerçant' }}
                    </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Téléphone</p>
                        <p class="font-medium">{{ $user->phone ?? 'Non renseigné' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Email vérifié</p>
                        <p class="font-medium">
                            @if($user->email_verified_at)
                                <span class="text-green-600">✅ Oui ({{ $user->email_verified_at->format('d/m/Y') }})</span>
                            @else
                                <span class="text-red-600">❌ Non</span>
                            @endif
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Statut</p>
                        <p class="font-medium">
                            @if($user->is_active)
                                <span class="text-green-600">● Actif</span>
                            @else
                                <span class="text-red-600">● Inactif</span>
                            @endif
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Inscrit le</p>
                        <p class="font-medium">{{ $user->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>

                <div class="mt-6 flex space-x-3">
                    <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-md text-sm hover:bg-yellow-600">
                            {{ $user->is_active ? 'Désactiver' : 'Activer' }}
                        </button>
                    </form>

                    <button type="button" onclick="openEditModal()"
                            class="px-4 py-2 bg-indigo-500 text-white rounded-md text-sm hover:bg-indigo-600">
                        <i class="fas fa-edit mr-1"></i> Modifier
                    </button>

                    @if($user->id !== 1)
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                              onsubmit="return confirm('Supprimer définitivement cet utilisateur ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md text-sm hover:bg-red-600">
                                <i class="fas fa-trash mr-1"></i> Supprimer
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Boutiques -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b">
                    <h3 class="font-semibold">Boutiques ({{ $user->shops->count() }})</h3>
                </div>

                @if($user->shops->isNotEmpty())
                    <div class="divide-y">
                        @foreach($user->shops as $shop)
                            <div class="px-6 py-4 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $shop->logo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($shop->name) . '&background=10b981&color=fff' }}"
                                         class="w-10 h-10 rounded-full">
                                    <div>
                                        <p class="font-medium">{{ $shop->name }}</p>
                                        <p class="text-sm text-gray-500">
                                            @if($shop->status === 'approved')
                                                <span class="text-green-600">Approuvée</span>
                                            @elseif($shop->status === 'pending')
                                                <span class="text-yellow-600">En attente</span>
                                            @else
                                                <span class="text-red-600">Rejetée</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('admin.shops.show', $shop) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                    <i class="fas fa-eye mr-1"></i> Voir
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-store text-3xl mb-2 block"></i>
                        Aucune boutique
                    </div>
                @endif
            </div>
        </div>

        <!-- Colonne latérale -->
        <div class="space-y-6">
            <!-- Actions rapides -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold mb-4">Actions</h3>
                <div class="space-y-2">
                    <a href="mailto:{{ $user->email }}" class="block w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm text-center hover:bg-gray-200">
                        <i class="fas fa-envelope mr-1"></i> Envoyer un email
                    </a>
                    @if($user->phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}" target="_blank"
                           class="block w-full px-4 py-2 bg-green-100 text-green-700 rounded-md text-sm text-center hover:bg-green-200">
                            <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                        </a>
                    @endif
                </div>
            </div>

            <!-- Statistiques -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold mb-4">Statistiques</h3>
                @php
                    $totalOrders = \App\Models\Order::whereIn('shop_id', $user->shops->pluck('id'))->count();
                    $totalRevenue = \App\Models\Order::whereIn('shop_id', $user->shops->pluck('id'))
                        ->where('payment_status', 'paid')->sum('total');
                @endphp
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Total commandes</span>
                        <span class="font-medium">{{ $totalOrders }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Revenus totaux</span>
                        <span class="font-medium">{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal édition -->
    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto" onclick="if(event.target === this) closeEditModal()">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full z-10 p-6">
                <h3 class="text-lg font-semibold mb-4">Modifier l'utilisateur</h3>
                <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium mb-1">Nom</label>
                        <input type="text" name="name" value="{{ $user->name }}" required
                               class="w-full border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" required
                               class="w-full border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Téléphone</label>
                        <input type="text" name="phone" value="{{ $user->phone }}"
                               class="w-full border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Rôle</label>
                        <select name="role" class="w-full border-gray-300 rounded-md">
                            <option value="merchant" {{ $user->role === 'merchant' ? 'selected' : '' }}>Commerçant</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Nouveau mot de passe (optionnel)</label>
                        <input type="password" name="password" class="w-full border-gray-300 rounded-md"
                               placeholder="Laisser vide pour ne pas changer">
                    </div>
                    <div class="flex justify-end space-x-2 pt-4">
                        <button type="button" onclick="closeEditModal()"
                                class="px-4 py-2 border rounded-md text-sm">Annuler</button>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openEditModal() {
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
@endpush
