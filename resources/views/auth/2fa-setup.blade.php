@extends('layouts.superadmin')

@section('content')
    <div class="max-w-md mx-auto p-6 mt-12">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <h2 class="text-xl font-bold mb-4" style="color: #4D1111">Configurer 2FA</h2>
            <p class="text-gray-600 mb-6">Scannez ce QR code avec Google Authenticator</p>

            <div class="mb-6">
                {!! $qrCode !!}
            </div>

            <p class="text-sm text-gray-500 mb-2">Ou entrez ce code manuellement :</p>
            <code class="bg-gray-100 px-4 py-2 rounded text-lg font-bold">{{ $secret }}</code>

            <a href="{{ route('admin.cashout.index') }}" class="block mt-6 text-white py-3 rounded-lg font-bold"
               style="background-color: #E81E25">
                ✅ J'ai configuré l'application
            </a>
        </div>
    </div>
@endsection
