@extends('merchant.layouts.app')

@section('title', 'Paniers abandonnés - ' . $shop->name)
@section('header', 'Paniers abandonnés - ' . $shop->name)

@section('content')

    @php
        $userPlan = auth()->user()->plan ?? 'free';
    @endphp

    @if($userPlan === 'free')
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.4); backdrop-filter: blur(4px);">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 p-8 max-w-md w-full text-center relative z-50">

                {{-- ❌ Bouton X pour fermer --}}
                <button onclick="this.closest('.fixed').remove(); document.querySelector('[x-data]').classList.remove('blur-sm', 'pointer-events-none', 'select-none')"
                        class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times"></i>
                </button>

                <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4 text-amber-600 text-4xl">
                    <i class="fas fa-crown"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Fonctionnalité Premium</h2>
                <p class="text-gray-500 mb-4">
                    Passez à un plan <strong>payant</strong> pour débloquer Suivi des paiements.
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
                   class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 text-white rounded-xl font-medium hover:bg-emerald-600 transition shadow-sm">
                    <i class="fas fa-rocket"></i>
                    Passer au plan Professionnel
                </a>
            </div>
        </div>
    @endif
    <div class="space-y-6 {{ $userPlan === 'free' ? 'blur-sm pointer-events-none select-none' : '' }}">

        <!-- Header mobile uniquement (sans sélecteur) -->
        <div class=" bg-white rounded-xl shadow-sm p-5 mb-6 -mt-4">
            <h1 class="text-2xl font-bold text-gray-900">Paniers abandonnés</h1>
            <p class="text-sm text-gray-500 mt-1">Récupérez les ventes perdues. Détectez automatiquement les clients qui quittent votre boutique sans finaliser leur commande.</p>
        </div>
        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex flex-col justify-between shadow-sm lg:shadow-none">
                <p class="text-sm text-gray-500">Total</p>
                <p class="mt-1 text-base lg:text-2xl font-bold">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex flex-col justify-between shadow-sm lg:shadow-none">
                <p class="text-sm text-gray-500">Récupérés</p>
                <p class="mt-1 text-base lg:text-2xl font-bold text-green-600">{{ $stats['recovered'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex flex-col justify-between shadow-sm lg:shadow-none">
                <p class="text-sm text-gray-500">Relancés</p>
                <p class="mt-1 text-base lg:text-2xl font-bold text-blue-600">{{ $stats['reminded'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 lg:border-gray-200 p-4 lg:p-5 flex flex-col justify-between shadow-sm lg:shadow-none">
                <p class="text-sm text-gray-500">En attente</p>
                <p class="mt-1 text-base lg:text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            </div>
        </div>

        {{-- Liste --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Version mobile : cartes -->
            <div class="md:hidden divide-y divide-gray-100">
                @forelse($carts as $cart)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-medium">{{ $cart->customer_name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">{{ $cart->customer_phone }}</p>
                            </div>
                            <div>
                                @if($cart->recovered)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">✅ Récupéré</span>
                                @elseif($cart->reminder_sent)
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">📩 Relancé</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">⏳ En attente</span>
                                @endif
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-sm mt-2">
                            <div>
                                <p class="text-xs text-gray-500">Total</p>
                                <p class="font-medium">{{ number_format($cart->total, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Date</p>
                                <p>{{ $cart->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="col-span-2 text-right mt-1">
                                @if($cart->customer_phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $cart->customer_phone) }}" target="_blank" class="text-green-600 hover:text-green-800 text-sm inline-flex items-center">
                                        <i class="fab fa-whatsapp mr-1"></i> Relancer
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">Aucun panier abandonné.</div>
                @endforelse
            </div>

            <!-- Version desktop : tableau -->
            <div class="hidden md:block">
                <table class="w-full">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    @forelse($carts as $cart)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $cart->customer_name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $cart->customer_phone }}</td>
                            <td class="px-4 py-3 font-medium">{{ number_format($cart->total, 0, ',', ' ') }} FCFA</td>
                            <td class="px-4 py-3">
                                @if($cart->recovered)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">✅ Récupéré</span>
                                @elseif($cart->reminder_sent)
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">📩 Relancé</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">⏳ En attente</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $cart->created_at->diffForHumans() }}</td>
                            <td class="px-4 py-3 text-right">
                                @if($cart->customer_phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $cart->customer_phone) }}" target="_blank" class="text-green-600 hover:text-green-800">
                                        <i class="fab fa-whatsapp"></i> Relancer
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-8 text-gray-500">Aucun panier abandonné.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="px-4 py-3">{{ $carts->links() }}</div>
            </div>
        </div>
    </div>
@endsection
