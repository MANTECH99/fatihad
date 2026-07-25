{{-- resources/views/admin/settings/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Paramètres')
@section('header', 'Paramètres')

@section('content')
    <div class="space-y-6">
        <!-- Général -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-6">Paramètres généraux</h3>
            <form action="{{ route('admin.settings.general') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nom du site</label>
                        <input type="text" name="site_name" value="{{ \App\Models\Setting::get('site_name', config('app.name')) }}"
                               class="w-full border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Devise</label>
                        <input type="text" name="currency" value="{{ \App\Models\Setting::get('currency', 'XOF') }}"
                               class="w-full border-gray-300 rounded-md">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Description</label>
                        <textarea name="site_description" rows="2" class="w-full border-gray-300 rounded-md">{{ \App\Models\Setting::get('site_description') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email de contact</label>
                        <input type="email" name="contact_email" value="{{ \App\Models\Setting::get('contact_email') }}"
                               class="w-full border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Téléphone</label>
                        <input type="text" name="contact_phone" value="{{ \App\Models\Setting::get('contact_phone') }}"
                               class="w-full border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Fuseau horaire</label>
                        <select name="timezone" class="w-full border-gray-300 rounded-md">
                            <option value="Africa/Dakar" {{ \App\Models\Setting::get('timezone', 'Africa/Dakar') === 'Africa/Dakar' ? 'selected' : '' }}>Dakar (GMT+0)</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md">Enregistrer</button>
            </form>
        </div>
    </div>
@endsection
