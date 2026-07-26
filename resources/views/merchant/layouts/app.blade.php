<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Commerçant') - {{ config('app.name') }}</title>

    {{-- PWA Manifest --}}
    <link rel="manifest" href="{{ asset('manifest-storefront.json') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ $shop->name ?? 'Seneshop' }}">
    <link rel="apple-touch-icon" href="{{ $shop->logo_url ?? asset('images/icons/icon-192x192.png') }}">

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @stack('styles')
    <style>
        .lucide{
            width:18px;
            height:18px;
            stroke-width:1.8;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100">
<div x-data="{ sidebarOpen: false }">
    <!-- Sidebar mobile -->
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 lg:hidden" @click="sidebarOpen = false">
        <div class="absolute inset-0 bg-black opacity-50"></div>
    </div>

    <div x-show="sidebarOpen" x-cloak
         class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform lg:hidden"
         @click.away="sidebarOpen = false">
        @include('merchant.layouts.sidebar')
    </div>

    <!-- Layout principal -->
    <div class="lg:flex">
        <!-- Sidebar desktop -->
        <div class="hidden lg:block w-64 min-h-screen bg-white shadow-lg">
            @include('merchant.layouts.sidebar')
        </div>

        <!-- Contenu -->
        <div class="flex-1 min-h-screen">
            <!-- Navbar -->
            <nav class="bg-white shadow-sm">
                <div class="w-full px-4 md:px-6">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500">
                                <i class="fas fa-bars text-xl"></i>
                            </button>
                            <h1 class="ml-4 text-xl font-semibold text-gray-800 lg:block hidden">
                                @yield('header', 'Tableau de bord')
                            </h1>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="lg:inline hidden">Selectionnez une boutique &nbsp;</span>
                            @php
                                $userShops = auth()->user()->shops;
                                $currentShopId = request('shop_id') ?? session('current_shop_id');
                            @endphp
                            @if($userShops->count() > 0)
                                <select onchange="changeShop(this.value)"
                                        class="text-sm border-gray-300 rounded-md">
                                    <option value="all" {{ $currentShopId === 'all' ? 'selected' : '' }}>Toutes les boutiques</option>
                                    @foreach($userShops as $shop)
                                        <option value="{{ $shop->id }}" {{ $currentShopId == $shop->id ? 'selected' : '' }}>
                                            {{ $shop->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <script>
                                    function changeShop(shopId) {
                                        if (shopId === 'all') {
                                            window.location.href = '{{ route('merchant.dashboard') }}?shop_id=all';
                                        } else {
                                            window.location.href = '{{ route('merchant.dashboard') }}?shop_id=' + shopId;
                                        }
                                    }
                                </script>
                            @endif

                            <!-- Menu utilisateur -->
                            <div x-data="{ open: false }" class="relative" x-cloak>
                                <button @click="open = !open" class="flex items-center space-x-2">
                                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name) }}"
                                         class="w-8 h-8 rounded-full" alt="Avatar">
                                </button>

                                <div x-show="open" @click.away="open = false" x-cloak
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
                                    <a href="{{ route('merchant.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">
                                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                                    </a>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i> Profil
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 text-red-600">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Contenu principal -->
            <main class="w-full px-4 py-6 md:px-6">
                <div class="text-center mb-8">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif
                </div>

                @yield('content')
            </main>
        </div>
    </div>
</div>

@stack('scripts')
<!-- Navbar fixe en bas (mobile uniquement) -->
<div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
    <div class="flex justify-around items-center h-16 px-2">
        {{-- Dashboard (global) --}}
        <a href="{{ route('merchant.dashboard') }}{{ $currentShopId && $currentShopId !== 'all' ? '?shop_id='.$currentShopId : '' }}"
           class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->routeIs('merchant.dashboard') ? 'text-emerald-600' : 'text-gray-500 hover:text-emerald-600' }}">
            <i data-lucide="layout-grid" class="w-5 h-5 mb-1"></i>
            <span class="text-[11px] font-medium">Dashboard</span>
        </a>

        {{-- Mes boutiques (global) --}}
        <a href="{{ route('merchant.shops.index') }}"
           class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->routeIs('merchant.shops.index') ? 'text-emerald-600' : 'text-gray-500 hover:text-emerald-600' }}">
            <i data-lucide="store" class="w-5 h-5 mb-1"></i>
            <span class="text-[11px] font-medium">Boutiques</span>
        </a>

        {{-- Abonnements (global) --}}
        <a href="{{ route('subscription.index') }}"
           class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->routeIs('subscription.index') ? 'text-emerald-600' : 'text-gray-500 hover:text-emerald-600' }}">
            <i data-lucide="credit-card" class="w-5 h-5 mb-1"></i>
            <span class="text-[11px] font-medium">Abonnements</span>
        </a>

        {{-- Paiements --}}
        @php
            $currentShopForPayments = $currentShopId && $currentShopId !== 'all'
                ? \App\Models\Shop::find($currentShopId)
                : auth()->user()->shops->first();
        @endphp

        @if($currentShopForPayments)
            <a href="{{ route('merchant.paiements.shop', $currentShopForPayments) }}"
               class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->routeIs('merchant.paiements.*') ? 'text-emerald-600' : 'text-gray-500 hover:text-emerald-600' }}">
                <i data-lucide="banknote" class="w-5 h-5 mb-1"></i>
                <span class="text-[11px] font-medium">Paiements</span>
            </a>
        @else
            <span class="flex flex-col items-center justify-center flex-1 py-1 text-gray-300 cursor-not-allowed">
        <i data-lucide="banknote" class="w-5 h-5 mb-1"></i>
        <span class="text-[11px] font-medium">Paiements</span>
    </span>
        @endif

        {{-- Paramètres --}}
        @php
            $currentShopForSettings = $currentShopId && $currentShopId !== 'all'
                ? \App\Models\Shop::find($currentShopId)
                : auth()->user()->shops->first();
        @endphp

        @if($currentShopForSettings)
            <a href="{{ route('merchant.shops.edit', $currentShopForSettings) }}"
               class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->routeIs('merchant.shops.edit') ? 'text-emerald-600' : 'text-gray-500 hover:text-emerald-600' }}">
                <i data-lucide="settings" class="w-5 h-5 mb-1"></i>
                <span class="text-[11px] font-medium">Paramètres</span>
            </a>
        @else
            <span class="flex flex-col items-center justify-center flex-1 py-1 text-gray-300 cursor-not-allowed">
        <i data-lucide="settings" class="w-5 h-5 mb-1"></i>
        <span class="text-[11px] font-medium">Paramètres</span>
    </span>
        @endif
    </div>
</div>

<!-- Padding en bas -->
<div class="lg:hidden h-16"></div>
<script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
<script>
    lucide.createIcons();
</script>

{{-- PWA Service Worker Dashboard Marchand --}}
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw-storefront.js', { scope: '/merchant/' })
                .then(function(reg) {
                    console.log('SW Dashboard enregistré:', reg.scope);
                })
                .catch(function(err) {
                    console.log('SW Dashboard échec:', err);
                });
        });
    }
</script>
</body>
</html>
