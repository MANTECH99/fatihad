<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration') - {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('styles')
</head>
<body class="bg-gray-100">
<div x-data="{ sidebarOpen: false }">
    <!-- Sidebar mobile -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-40 lg:hidden" @click="sidebarOpen = false">
        <div class="absolute inset-0 bg-black opacity-50"></div>
    </div>

    <div x-show="sidebarOpen"
         class="fixed inset-y-0 left-0 z-50 w-64 bg-indigo-900 transform transition-transform lg:hidden"
         @click.away="sidebarOpen = false">
        @include('admin.layouts.sidebar')
    </div>

    <div class="lg:flex">
        <!-- Sidebar desktop -->
        <div class="hidden lg:block w-64 min-h-screen bg-indigo-900">
            @include('admin.layouts.sidebar')
        </div>

        <!-- Contenu -->
        <div class="flex-1 min-h-screen">
            <nav class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500">
                                <i class="fas fa-bars text-xl"></i>
                            </button>
                            <h1 class="ml-4 text-xl font-semibold">@yield('header')</h1>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('merchant.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                <i class="fas fa-store mr-1"></i> Vue commerçant
                            </a>
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="flex items-center space-x-2">
                                    <span class="text-sm">{{ auth()->user()->name }}</span>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
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

                @yield('content')
            </main>
        </div>
    </div>
</div>

@stack('scripts')
</body>
</html>
