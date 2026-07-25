{{-- resources/views/merchant/upgrade.blade.php --}}
@extends('merchant.layouts.app')

@section('title', 'Limite atteinte')
@section('header', 'Limite atteinte')

@section('content')
    <div class="max-w-lg mx-auto text-center py-12">
        <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-exclamation-triangle text-3xl text-yellow-500"></i>
        </div>
        <h2 class="text-2xl font-bold mb-2">{{ $message ?? 'Limite de votre plan atteinte' }}</h2>
        <p class="text-gray-500 mb-6">{{ $detail ?? 'Passez à un plan supérieur pour continuer.' }}</p>
        <a href="{{ route('subscription.index') }}" class="inline-block bg-emerald-500 text-white px-8 py-3 rounded-xl font-bold hover:bg-emerald-600">
            Voir les plans
        </a>
    </div>
@endsection
