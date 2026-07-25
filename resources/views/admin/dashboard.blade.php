{{-- resources/views/admin/dashboard.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Tableau de bord')
@section('header', 'Tableau de bord')

@section('content')
    <div class="space-y-6">
        <!-- Statistiques -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-store text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Boutiques</p>
                        <p class="text-2xl font-bold">{{ $stats['active_shops'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Commandes aujourd'hui</p>
                        <p class="text-2xl font-bold">{{ $stats['orders_today'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-money-bill-wave text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Revenus aujourd'hui</p>
                        <p class="text-2xl font-bold">{{ number_format($stats['revenue_today'], 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-users text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Utilisateurs</p>
                        <p class="text-2xl font-bold">{{ $stats['total_users'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Commandes (7 derniers jours)</h2>
            <canvas id="ordersChart" height="80"></canvas>
        </div>

        <!-- Tableaux -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top boutiques -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold">Top boutiques</h2>
                </div>
                <div class="p-6">
                    <table class="w-full">
                        <thead>
                        <tr class="text-left text-sm text-gray-500">
                            <th class="pb-2">Boutique</th>
                            <th class="pb-2">Commandes</th>
                            <th class="pb-2">Revenus</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($topShops as $shop)
                            <tr class="border-t">
                                <td class="py-2">
                                    <a href="{{ route('admin.shops.show', $shop) }}" class="text-blue-600 hover:underline">
                                        {{ $shop->name }}
                                    </a>
                                </td>
                                <td class="py-2">{{ $shop->orders_count }}</td>
                                <td class="py-2">{{ number_format($shop->total_revenue ?? 0, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Dernières commandes -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b flex justify-between items-center">
                    <h2 class="text-lg font-semibold">Dernières commandes</h2>
                    <a href="{{ route('admin.orders.index') }}" class="text-sm text-blue-600 hover:underline">Voir tout</a>
                </div>
                <div class="p-6">
                    <table class="w-full">
                        <thead>
                        <tr class="text-left text-sm text-gray-500">
                            <th class="pb-2">N°</th>
                            <th class="pb-2">Client</th>
                            <th class="pb-2">Statut</th>
                            <th class="pb-2">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($recentOrders as $order)
                            <tr class="border-t">
                                <td class="py-2 text-sm">{{ $order->order_number }}</td>
                                <td class="py-2 text-sm">{{ $order->customer_name }}</td>
                                <td class="py-2">
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $order->getStatusColor() }}-100 text-{{ $order->getStatusColor() }}-800">
                                    {{ $order->getStatusLabel() }}
                                </span>
                                </td>
                                <td class="py-2 text-sm">{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const ctx = document.getElementById('ordersChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($ordersChart->pluck('date')) !!},
                datasets: [{
                    label: 'Nombre de commandes',
                    data: {!! json_encode($ordersChart->pluck('count')) !!},
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
@endpush
