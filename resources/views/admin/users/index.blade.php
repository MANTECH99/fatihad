{{-- resources/views/admin/users/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Utilisateurs')
@section('header', 'Gestion des utilisateurs')

@section('content')
    <div x-data="{ showCreateModal: false }">
        <div class="flex justify-between items-center mb-6">
            <div class="flex space-x-2">
                <form action="{{ route('admin.users.index') }}" method="GET" class="flex space-x-2">
                    <input type="text" name="search" placeholder="Rechercher..." value="{{ request('search') }}"
                           class="border-gray-300 rounded-md text-sm">
                    <select name="role" class="border-gray-300 rounded-md text-sm">
                        <option value="">Tous</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="merchant" {{ request('role') == 'merchant' ? 'selected' : '' }}>Commerçant</option>
                    </select>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm">Filtrer</button>
                </form>
            </div>
            <button @click="showCreateModal = true" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm">
                <i class="fas fa-plus mr-2"></i> Ajouter
            </button>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rôle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Boutiques</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @foreach($users as $user)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=6366f1&color=fff"
                                     class="w-10 h-10 rounded-full mr-3">
                                <div>
                                    <p class="font-medium">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $user->role === 'admin' ? 'Admin' : 'Commerçant' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $user->shops_count }}</td>
                        <td class="px-6 py-4">
                            <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-2 py-1 text-xs rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-800 mr-2">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($user->id !== 1)
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4">
                {{ $users->links() }}
            </div>
        </div>

        <!-- Modal création -->
        <div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="showCreateModal = false">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-black opacity-50"></div>
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full z-10 p-6">
                    <h3 class="text-lg font-semibold mb-4">Ajouter un utilisateur</h3>
                    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium mb-1">Nom</label>
                            <input type="text" name="name" required class="w-full border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Email</label>
                            <input type="email" name="email" required class="w-full border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Téléphone</label>
                            <input type="text" name="phone" class="w-full border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Mot de passe</label>
                            <input type="password" name="password" required class="w-full border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Rôle</label>
                            <select name="role" class="w-full border-gray-300 rounded-md">
                                <option value="merchant">Commerçant</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" @click="showCreateModal = false" class="px-4 py-2 border rounded-md">Annuler</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Créer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
