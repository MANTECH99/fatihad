@extends('merchant.layouts.app')

@section('title', 'Fonctionnalité Premium')
@section('header', 'Fonctionnalité Premium')

@section('content')
    <div class="max-w-3xl mx-auto py-12 px-4 text-center">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8">
            <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4 text-emerald-600 text-4xl">
                <i class="fas fa-crown"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Fonctionnalité Premium</h2>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">
                Passez à un plan <strong>payant</strong> pour débloquer cette fonctionnalité.
            </p>

            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <p class="text-sm text-gray-500">Votre plan actuel</p>
                @php
                    $planKey = auth()->user()->plan ?? 'free';
                    $planName = \App\Services\PlanService::$plans[$planKey]['name'] ?? 'Gratuit';
                @endphp
                <p class="text-xl font-bold text-gray-800 uppercase">
                    {{ $planName }}
                </p>
            </div>

            <a href="{{ route('subscription.index') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white rounded-xl font-medium hover:bg-emerald-700 transition shadow-sm">
                <i class="fas fa-rocket"></i>
                Passer au plan Professionnel
            </a>
        </div>
    </div>
@endsection
