@extends('layouts.superadmin')

@section('content')
    <div class="max-w-lg mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6" style="color: #4D1111">Créer un sous-marchand</h1>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.sub-merchants.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Nom interne</label>
                    <input type="text" name="name" required class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Nom commercial (affiché au client)</label>
                    <input type="text" name="commercial_name" required class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Site</label>
                    <select name="site" required class="w-full border rounded px-3 py-2">
                        <option value="disso">ASC Disso</option>
                        <option value="caravane">Caravane</option>
                        <option value="caravane">Seneshop</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium mb-1">Pays</label>
                    <div class="space-x-4">
                        <label><input type="checkbox" name="countries[]" value="SN" checked> Sénégal</label>
                        <label><input type="checkbox" name="countries[]" value="CI"> Côte d'Ivoire</label>
                    </div>
                </div>

                <button type="submit" class="w-full text-white py-3 rounded-lg font-bold" style="background-color: #E81E25">
                    Créer
                </button>
            </form>
        </div>
    </div>
@endsection
