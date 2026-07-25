<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4D1111">
    <title>Super Admin - ASC Disso</title>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-dark: #4D1111;
            --primary-red: #E81E25;
            --dark-bg: #181A1C;
            --light-gray: #D3D4D2;
            --white: #FFFFFF;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            min-height: 100vh;
        }

        /* Sidebar Desktop */
        .sidebar {
            background-color: var(--dark-bg);
            min-height: 100vh;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 40;
            transition: transform 0.3s ease;
        }

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: var(--light-gray);
            transition: all 0.3s;
            border-left: 3px solid transparent;
            font-weight: 500;
            text-decoration: none;
        }

        .nav-link:hover {
            background-color: rgba(232, 30, 37, 0.2);
            color: var(--white);
            border-left-color: var(--primary-red);
        }

        .nav-link.active {
            background-color: var(--primary-red);
            color: var(--white);
            border-left-color: var(--white);
        }

        /* Mobile Header */
        .mobile-header {
            display: none;
            background-color: var(--dark-bg);
            color: white;
            padding: 12px 16px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .hamburger {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 4px 8px;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 39;
        }

        .sidebar-overlay.active {
            display: block;
        }

        /* Mobile */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 270px;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .logout-mobile {
                display: block;
            }

            .logout-desktop {
                display: none;
            }
        }

        @media (min-width: 769px) {
            .logout-mobile {
                display: none;
            }

            .logout-desktop {
                display: block;
            }
        }
    </style>
</head>
<body>
{{-- Overlay mobile --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

{{-- Mobile Header --}}
<div class="mobile-header">
    <button class="hamburger" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    <div class="flex items-center space-x-2">
        <img src="{{ asset('images/logo.png') }}" alt="ASC Disso" class="h-8 w-8 rounded-full">
        <span class="font-bold">Seneshop</span>
    </div>
    <span class="text-xs text-red-500 font-medium">🔐 Super Admin</span>
</div>

<div class="flex flex-col md:flex-row min-h-screen">
    {{-- Sidebar --}}
    <div class="sidebar" id="sidebar">
        <div class="p-4 border-b border-gray-700 hidden md:block">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/logo.png') }}" alt="ASC Disso" class="h-10 w-10 rounded-full">
                <div>
                    <span class="text-white font-bold text-lg">Seneshop</span>
                    <span class="block text-xs font-medium" style="color: var(--primary-red)">🔐 Super Admin</span>
                </div>
            </div>
        </div>

        <div class="p-4 border-b border-gray-700 md:hidden">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo.png') }}" alt="ASC Disso" class="h-10 w-10 rounded-full">
                    <div>
                        <span class="text-white font-bold">ASC Disso</span>
                        <span class="block text-xs" style="color: var(--primary-red)">🔐 Super Admin</span>
                    </div>
                </div>
                <button onclick="closeSidebar()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        {{-- Infos utilisateur mobile --}}
        <div class="px-4 py-3 border-b border-gray-700 md:hidden">
            <div class="text-sm text-gray-400">👤 {{ Auth::user()->name }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ Auth::user()->email }}</div>
        </div>

        <nav class="mt-4 flex flex-col flex-1">
            <div>
                <a href="{{ route('admin.cashout.index') }}"
                   class="nav-link {{ request()->routeIs('admin.cashout.*') ? 'active' : '' }}">
                    <span class="mr-3 text-lg">💰</span> Cashout
                </a>
            </div>
            <div>
                <a href="{{ route('admin.sub-merchants.index') }}"
                   class="nav-link {{ request()->routeIs('admin.sub-merchants.*') ? 'active' : '' }}">
                    <span class="mr-3 text-lg">🏪</span> Sous-marchands
                </a>
            </div>
            <div>
                <a href="#" onclick="prompt2FA(event)"
                   class="nav-link">
                    <span class="mr-3 text-lg">🔐</span> Activer 2FA
                </a>
            </div>

            <div>
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-link {{ request()->routeIs('admin.dashboard')  ? 'active' : '' }}">
                    <span class="mr-3 text-lg">🏪</span> Dashboard
                </a>
            </div>
            {{-- Déconnexion desktop --}}
            <div class="logout-desktop mt-auto">
                <div class="border-t border-gray-700 mx-4 my-4"></div>
                <div class="px-4 pb-2">
                    <div class="text-xs text-gray-500 mb-2">
                        👤 {{ Auth::user()->name }}
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="px-2 pb-4">
                    @csrf
                    <button type="submit"
                            class="w-full text-left text-sm text-gray-400 hover:text-white transition py-2 px-3 rounded hover:bg-gray-800">
                        🚪 Déconnexion
                    </button>
                </form>
            </div>

            {{-- Déconnexion mobile --}}
            <div class="logout-mobile mt-auto border-t border-gray-700 mx-4 pt-4 pb-6">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full text-left text-sm text-gray-400 hover:text-white transition py-3 px-4 rounded hover:bg-gray-800 flex items-center">
                        <span class="mr-3">🚪</span> Déconnexion
                    </button>
                </form>
            </div>
        </nav>
    </div>

    {{-- Contenu principal --}}
    <div class="main-content flex-1" id="mainContent">
        {{-- Messages flash --}}
        @if(session('success'))
            <div class="bg-green-50 border-b-2 border-green-500 text-green-700 px-4 md:px-6 py-3 md:py-4">
                <div class="flex items-center space-x-3">
                    <span class="text-xl">✅</span>
                    <span class="text-sm md:text-base">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-b-2 border-red-500 text-red-700 px-4 md:px-6 py-3 md:py-4">
                <div class="flex items-center space-x-3">
                    <span class="text-sm md:text-base">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Contenu de la page --}}
        @yield('content')
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
        document.getElementById('sidebarOverlay').classList.toggle('active');
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('active');
        document.getElementById('sidebarOverlay').classList.remove('active');
    }

    // Fermer la sidebar si on clique en dehors
    document.getElementById('mainContent').addEventListener('click', function(e) {
        if (document.getElementById('sidebar').classList.contains('active')) {
            closeSidebar();
        }
    });

    function prompt2FA(e) {
        e.preventDefault();
        const password = prompt('Mot de passe :');
        if (password) {
            fetch('{{ route("2fa.check-password") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ password: password })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = "{{ route('2fa.setup') }}";
                    } else {
                        alert('Mot de passe incorrect.');
                    }
                });
        }
    }
</script>
</body>
</html>
