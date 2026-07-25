@extends('merchant.layouts.app')

@section('title', 'Tableau de bord')
@section('header', $currentShop ? 'Tableau de bord - ' . $currentShop->name : 'Tableau de bord - Toutes les boutiques')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
    <div class="space-y-6">


        @php
            $subscription = \App\Models\Subscription::where('user_id', auth()->id())
                ->where('status', 'active')
                ->latest()
                ->first();
            $planKey = auth()->user()->plan ?? 'free';
            $planName = \App\Services\PlanService::$plans[$planKey]['name'] ?? 'Gratuit';

            $joursRestants = null;
            if ($subscription && $subscription->ends_at) {
                $joursRestants = now()->diffInDays($subscription->ends_at, false);
            } elseif (auth()->user()->trial_ends_at && auth()->user()->trial_ends_at->isFuture()) {
                $joursRestants = now()->diffInDays(auth()->user()->trial_ends_at, false);
            }
        @endphp
        <h1 class="lg:hidden text-xl font-bold text-gray-900">
            Tableau de bord{{ isset($currentShop) && $currentShop ? ' - ' . $currentShop->name : ' - Toutes les boutiques' }}
        </h1>
        @if($joursRestants !== null && $joursRestants <= 3)
            <span class="inline-flex items-center gap-1 bg-red-50 border border-red-200 rounded-lg px-2 py-1 text-xs text-red-700 sm:hidden">
        <i class="fas fa-exclamation-triangle text-red-500 text-[10px]"></i>
        Cher client (e) , votre abonnement expire bientôt
    </span>
        @endif
        <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full {{ $planKey === 'free' ? 'bg-gray-100' : 'bg-emerald-100' }} flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-crown {{ $planKey === 'free' ? 'text-emerald-500' : 'text-emerald-600' }}"></i>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-base lg:text-xl  text-gray-500 font-medium">Abonnement actuel :</span>
                        <span class="text-base lg:text-xl  font-bold text-emerald-500">{{ $planName }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if($joursRestants !== null && $joursRestants > 0)
                        <div class="flex items-center gap-2">
                            {{-- Mobile --}}
                            <span class="sm:hidden text-sm font-medium text-gray-500">{{ (int) $joursRestants }} jour(s) restants</span>

                            {{-- Desktop --}}
                            <span class="hidden sm:inline text-sm font-medium text-gray-500">Cher <strong>Client</strong>, vous avez {{ (int) $joursRestants }} jour(s) restants</span>
                            @if($joursRestants <= 3)
                                <span class="hidden md:inline-flex items-center gap-1 bg-red-50 border border-red-200 rounded-lg px-2 py-1 text-xs text-red-700">
        <i class="fas fa-exclamation-triangle text-red-500 text-[10px]"></i>
        Votre abonnement expire bientôt
    </span>
                            @endif
                        </div>
                    @endif

                    <a href="{{ route('subscription.index') }}"
                       class="inline-flex items-center gap-1 px-4 py-2 {{ $planKey === 'free' ? 'bg-emerald-500 hover:bg-emerald-600 text-white' : 'bg-emerald-500 hover:bg-emerald-600 text-white' }} text-sm font-medium rounded-full transition whitespace-nowrap">
                        {{ $planKey === 'free' ? 'Passer au plan Payant' : 'Gestion abonnement' }}
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats - cartes arrondies style mobile -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Boutiques</p>
                        <p class="mt-1 text-xl font-bold text-gray-900">{{ $shops->count() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center">
                        <i data-lucide="store" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Commandes</p>
                        <p class="mt-1 text-sm lg:text-xl font-bold text-gray-900">{{ $totalOrders }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                        <i data-lucide="shopping-cart" class="w-5 h-5 text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Revenus</p>
                        <p class="mt-1 text-sm lg:text-xl font-bold text-gray-900">{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center">
                        <i data-lucide="wallet" class="w-5 h-5 text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium">En attente</p>
                        <p class="mt-1 text-sm lg:text-xl font-bold text-yellow-600">{{ $pendingOrders }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-yellow-50 flex items-center justify-center">
                        <i data-lucide="clock-3" class="w-5 h-5 text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Graphique des commandes par jour (7 derniers jours) --}}
            <div class="bg-white rounded-lg shadow p-6" style="max-height: 400px;">
                <h3 class="text-lg font-semibold mb-4">Commandes (7 derniers jours)</h3>
                <canvas id="ordersChart" height="100"></canvas>
            </div>

            {{-- Graphique des revenus par jour (7 derniers jours) --}}
            <div class="bg-white rounded-lg shadow p-6" style="max-height: 400px;">
                <h3 class="text-lg font-semibold mb-4">Revenus (7 derniers jours)</h3>
                <canvas id="revenueChart" height="100"></canvas>
            </div>

            {{-- Répartition par statut --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Commandes par statut</h3>
                <div style="height: 250px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            {{-- Top boutiques --}}
            <div class="bg-white rounded-lg shadow p-6" style="max-height: 400px;">
                <h3 class="text-lg font-semibold mb-4">Revenus par boutique</h3>
                <canvas id="shopsChart" height="100"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Commandes par jour
        new Chart(document.getElementById('ordersChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($ordersChartLabels) !!},
                datasets: [{
                    label: 'Commandes',
                    data: {!! json_encode($ordersChartData) !!},
                    backgroundColor: '#10b981',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // Revenus par jour
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($revenueChartLabels) !!},
                datasets: [{
                    label: 'Revenus (FCFA)',
                    data: {!! json_encode($revenueChartData) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Par statut
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($statusLabels) !!},
                datasets: [{
                    data: {!! json_encode($statusData) !!},
                    backgroundColor: ['#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#6b7280']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // ← Pour le doughnut, garder le ratio
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // Par boutique
        new Chart(document.getElementById('shopsChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($shopsChartLabels) !!},
                datasets: [{
                    label: 'Revenus (FCFA)',
                    data: {!! json_encode($shopsChartData) !!},
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
@endpush
