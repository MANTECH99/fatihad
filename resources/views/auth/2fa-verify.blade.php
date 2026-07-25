@extends('layouts.superadmin')

@section('content')
    <div class="max-w-md mx-auto p-6 mt-12">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-6">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
                     style="background-color: #E81E25;">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold" style="color: #4D1111">Authentification requise</h2>
                <p class="text-gray-600 text-sm mt-2">Saisissez votre code 2FA</p>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('2fa.verify') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <input type="text" name="code" required
                           placeholder="123 456"
                           maxlength="6"
                           class="w-full text-center text-3xl tracking-widest border-2 border-gray-300 rounded-lg py-4 focus:outline-none focus:border-red-500"
                           style="letter-spacing: 15px;">
                    <p class="text-xs text-gray-400 mt-2 text-center">Entrez le code à 6 chiffres</p>
                </div>

                <button type="submit"
                        class="w-full text-white py-3 rounded-lg font-bold transition"
                        style="background-color: #4D1111">
                    Vérifier
                </button>
            </form>
        </div>
    </div>
@endsection
