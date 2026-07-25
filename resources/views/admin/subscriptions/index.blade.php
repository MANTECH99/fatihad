{{-- resources/views/admin/subscriptions/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Abonnements')
@section('header', 'Abonnements')

@section('content')
    <div class="space-y-6">

        {{-- Compteurs --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Actifs</p>
                        <p class="text-2xl font-bold">{{ $subscriptions->where('status', 'active')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">En attente</p>
                        <p class="text-2xl font-bold">{{ $subscriptions->where('status', 'pending')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-full">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Annulés</p>
                        <p class="text-2xl font-bold">{{ $subscriptions->where('status', 'cancelled')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tableau --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold">Tous les abonnements</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commerçant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Début</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expire le</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @forelse($subscriptions as $subscription)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $subscription->user->name ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-500">{{ $subscription->user->email ?? '' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $subscription->user->phone ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $subscription->plan === 'business' ? 'bg-purple-100 text-purple-700' :
                                       ($subscription->plan === 'starter' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                    {{ ucfirst($subscription->plan) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($subscription->status === 'active')
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Actif</span>
                                @elseif($subscription->status === 'pending')
                                    <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">En attente</span>
                                @elseif($subscription->status === 'cancelled')
                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Annulé</span>
                                @elseif($subscription->status === 'expired')
                                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Expiré</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $subscription->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $subscription->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($subscription->ends_at)
                                    <span class="{{ $subscription->ends_at->isPast() ? 'text-red-500' : 'text-gray-500' }}">
                                        {{ $subscription->ends_at->format('d/m/Y') }}
                                    </span>
                                @elseif($subscription->trial_ends_at)
                                    <span class="{{ $subscription->trial_ends_at->isPast() ? 'text-red-500' : 'text-yellow-600' }}">
                                        Essai jusqu'au {{ $subscription->trial_ends_at->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($subscription->status === 'active')
                                    <form action="{{ route('admin.subscriptions.cancel', $subscription) }}" method="POST" onsubmit="return confirm('Annuler cet abonnement ?')">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                            Annuler
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 text-sm">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                Aucun abonnement trouvé.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t">
                {{ $subscriptions->links() }}
            </div>
        </div>
    </div>
@endsection
