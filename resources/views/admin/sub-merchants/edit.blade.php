@extends('layouts.superadmin')

@section('content')
    <div class="max-w-lg mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6" style="color: #4D1111">Modifier le sous-marchand</h1>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.sub-merchants.update', $subMerchant) }}" method="POST">
                @csrf @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Nom commercial</label>
                    <input type="text" name="commercial_name" value="{{ $subMerchant->commercial_name }}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Site</label>
                    <select name="site" class="w-full border rounded px-3 py-2">
                        <option value="disso" {{ $subMerchant->site === 'disso' ? 'selected' : '' }}>ASC Disso</option>
                        <option value="caravane" {{ $subMerchant->site === 'caravane' ? 'selected' : '' }}>Caravane</option>
                        <option value="Seneshop" {{ $subMerchant->site === 'Seneshop' ? 'selected' : '' }}>Seneshop</option>
                    </select>
                </div>

                <button type="submit" class="w-full text-white py-3 rounded-lg font-bold" style="background-color: #E81E25">
                    Mettre à jour
                </button>
            </form>
        </div>
    </div>
@endsection
