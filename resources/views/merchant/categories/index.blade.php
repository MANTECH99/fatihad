{{-- resources/views/merchant/categories/index.blade.php --}}
@extends('merchant.layouts.app')

@section('title', 'Catégories - ' . $shop->name)
@section('header', 'Catégories - ' . $shop->name)

@section('content')
    <div x-data="categoryManager()">
        <div class="flex justify-between items-center mb-6 -mt-4">
            <a href="{{ route('merchant.products.index', $shop) }}" class="text-sm text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-1"></i> Retour aux produits
            </a>
            <button @click="showForm = true; editingId = null; resetForm()"
                    class="bg-emerald-500 text-white px-4 py-2 rounded-md text-sm hover:bg-emerald-600">
                <i class="fas fa-plus mr-2"></i> Nouvelle catégorie
            </button>
        </div>

        <!-- Formulaire -->
        <div x-show="showForm" x-cloak class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="font-semibold mb-4" x-text="editingId ? 'Modifier la catégorie' : 'Nouvelle catégorie'"></h3>

            <form :action="editingId ? '{{ route('merchant.categories.update', ['shop' => $shop, 'category' => '__ID__']) }}'.replace('__ID__', editingId) : '{{ route('merchant.categories.store', $shop) }}'"
                  method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <template x-if="editingId">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-sm font-medium mb-1">Nom *</label>
                    <input type="text" name="name" x-model="formData.name" required
                           class="w-full border-gray-300 rounded-md" placeholder="ex: Boissons">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea name="description" x-model="formData.description" rows="2"
                              class="w-full border-gray-300 rounded-md"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Image</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-emerald-50 file:text-emerald-700">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" x-model="formData.is_active"
                           class="rounded border-gray-300 text-emerald-500">
                    <label class="ml-2 text-sm">Active</label>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" @click="showForm = false" class="px-4 py-2 border rounded-md text-sm">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-emerald-500 text-white rounded-md text-sm">
                        <i class="fas fa-save mr-1"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Liste -->
        <div class="bg-white rounded-lg shadow overflow-hidden">

            @if($categories->isNotEmpty())
                <!-- Version mobile : cartes -->
                <div class="md:hidden divide-y divide-gray-200">
                    @foreach($categories as $category)
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center">
                                    @if($category->image_url)
                                        <img src="{{ $category->image_url }}" class="w-12 h-12 rounded object-cover mr-3">
                                    @endif
                                    <div>
                                        <p class="font-medium">{{ $category->name }}</p>
                                        @if($category->description)
                                            <p class="text-sm text-gray-500">{{ Str::limit($category->description, 50) }}</p>
                                        @endif
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                            </div>

                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Produits : <span class="font-medium text-gray-900">{{ $category->products_count }}</span></p>
                            </div>

                            <div class="flex justify-end space-x-3 mt-3 pt-3 border-t border-gray-100">
                                <button @click="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->description }}', {{ $category->is_active ? 'true' : 'false' }})"
                                        class="text-blue-600 hover:text-blue-800 text-sm inline-flex items-center">
                                    <i class="fas fa-edit mr-1"></i> Modifier
                                </button>
                                <form action="{{ route('merchant.categories.destroy', ['shop' => $shop, 'category' => $category]) }}"
                                      method="POST" class="inline" onsubmit="return confirm('Supprimer cette catégorie ? Les produits seront détachés.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm inline-flex items-center">
                                        <i class="fas fa-trash mr-1"></i> Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Version desktop : tableau -->
                <div class="hidden md:block">
                <table class="w-full">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produits</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    @foreach($categories as $category)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($category->image_url)
                                        <img src="{{ $category->image_url }}" class="w-10 h-10 rounded object-cover mr-3">
                                    @endif
                                    <div>
                                        <p class="font-medium">{{ $category->name }}</p>
                                        @if($category->description)
                                            <p class="text-sm text-gray-500">{{ Str::limit($category->description, 50) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $category->products_count }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button @click="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->description }}', {{ $category->is_active ? 'true' : 'false' }})"
                                        class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('merchant.categories.destroy', ['shop' => $shop, 'category' => $category]) }}"
                                      method="POST" class="inline" onsubmit="return confirm('Supprimer cette catégorie ? Les produits seront détachés.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-tags text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Aucune catégorie. Créez-en pour organiser vos produits.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function categoryManager() {
            return {
                showForm: false,
                editingId: null,
                formData: {
                    name: '',
                    description: '',
                    is_active: true
                },

                resetForm() {
                    this.formData = { name: '', description: '', is_active: true };
                    this.editingId = null;
                },

                editCategory(id, name, description, isActive) {
                    this.showForm = true;
                    this.editingId = id;
                    this.formData = { name, description, is_active: isActive };
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        }
    </script>
@endpush
