<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Marketplace') - FatiHad</title>
    {{-- PWA Manifest --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="FatiHad">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="theme-color" content="#10B981">
    @stack('styles')

    <style>
        .overflow-x-auto::-webkit-scrollbar { display: none; }
        .overflow-x-auto { -ms-overflow-style: none; scrollbar-width: none; }
        .search-input { border: none !important; outline: none !important; box-shadow: none !important; -webkit-appearance: none; appearance: none; background: transparent; }
        .search-input:focus { border: none !important; outline: none !important; box-shadow: none !important; }
        [x-cloak] { display: none !important; }
    </style>
    <script src="https://dashboard.causeriebot.com/widget.js" data-id="5924dd18-3bb4-4ee5-b684-34dddf8bb1f0" data-url="https://dashboard.causeriebot.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

<header class="bg-white shadow-sm sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-3">
        {{-- Logo + Nom --}}
        <a href="{{ route('marketplace.public.home') }}" class="inline-flex items-center font-bold text-gray-900 text-base flex-shrink-0 gap-2">
            <img src="{{ asset('images/fatihad.png') }}" alt="Seneshop" class="h-8 w-auto -mt-2"> FatiHad
        </a>

        {{-- Barre de recherche globale --}}
        <form action="{{ route('marketplace.public.home') }}" method="GET" class="flex-1 min-w-0">
            <div class="flex items-center h-11 bg-white border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                <div class="flex items-center justify-center px-3 text-gray-400">
                    <i class="fas fa-search text-base"></i>
                </div>
                <input type="text" name="q" placeholder="Rechercher une boutique, un produit..." autocomplete="off" class="search-input flex-1 w-full h-full px-2 text-sm text-gray-700 placeholder-gray-400">
                <button type="submit" class="hidden sm:flex h-full px-6 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium border-0 outline-none focus:outline-none focus:ring-0 transition items-center">
                    Rechercher
                </button>
            </div>
        </form>

        <div class="hidden sm:flex items-center gap-2 flex-shrink-0">
            <a href="#" class="flex items-center gap-1.5 px-3 py-1.5 border-2 border-gray-500 text-black-500 text-sm font-medium rounded-full bg-transparent hover:bg-orange-50 transition whitespace-nowrap">
                <i class="fas fa-map-marker-alt text-xs"></i> Senegal
            </a>
            <a href="{{ route('register') }}" class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-full transition whitespace-nowrap">
                <i class="fas fa-plus-circle"></i> Vendre
            </a>
        </div>
    </div>
    {{-- ========== BARRE DE NAVIGATION SOUS LE HEADER ========== --}}
    <div x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            {{-- Desktop --}}
            <nav class="hidden md:flex items-center justify-between py-3">
                <a href="{{ route('marketplace.public.home') }}"
                   class="relative font-bold hover:text-emerald-600 transition whitespace-nowrap pb-1 {{ request()->routeIs('marketplace.public.home') ? 'text-emerald-600' : 'text-gray-600' }}">
                    <i class="fas fa-home mr-2 {{ request()->routeIs('marketplace.public.home') ? 'text-emerald-600' : 'text-emerald-600' }}"></i>ACCUEIL
                    @if(request()->routeIs('marketplace.public.home'))
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-500 rounded-full"></span>
                    @endif
                </a>
                <a href="{{ route('marketplace.public.all-products') }}"
                   class="relative font-bold hover:text-emerald-600 transition whitespace-nowrap pb-1 {{ request()->routeIs('marketplace.public.all-products') ? 'text-emerald-600' : 'text-gray-600' }}">
                    <i class="fas fa-box mr-2 {{ request()->routeIs('marketplace.public.all-products') ? 'text-emerald-600' : 'text-emerald-600' }}"></i>TOUS LES PRODUITS
                    @if(request()->routeIs('marketplace.public.all-products'))
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-500 rounded-full"></span>
                    @endif
                </a>
                <a href="{{ route('marketplace.public.promotions') }}"
                   class="relative font-bold hover:text-emerald-600 transition whitespace-nowrap pb-1 {{ request()->routeIs('marketplace.public.promotions') ? 'text-emerald-600' : 'text-gray-600' }}">
                    <i class="fas fa-tag mr-2 {{ request()->routeIs('marketplace.public.promotions') ? 'text-emerald-600' : 'text-emerald-600' }}"></i>PROMOTIONS
                    @if(request()->routeIs('marketplace.public.promotions'))
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-500 rounded-full"></span>
                    @endif
                </a>
                <a href="{{ route('marketplace.public.nouveautes') }}"
                   class="relative font-bold hover:text-emerald-600 transition whitespace-nowrap pb-1 {{ request()->routeIs('marketplace.public.nouveautes') ? 'text-emerald-600' : 'text-gray-600' }}">
                    <i class="fas fa-star mr-2 {{ request()->routeIs('marketplace.public.nouveautes') ? 'text-emerald-600' : 'text-emerald-600' }}"></i>NOUVEAUTÉS
                    @if(request()->routeIs('marketplace.public.nouveautes'))
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-500 rounded-full"></span>
                    @endif
                </a>
                <a href="{{ route('marketplace.public.shops') }}"
                   class="relative font-bold hover:text-emerald-600 transition whitespace-nowrap pb-1 {{ request()->routeIs('marketplace.public.shops') ? 'text-emerald-600' : 'text-gray-600' }}">
                    <i class="fas fa-store mr-2 {{ request()->routeIs('marketplace.public.shops') ? 'text-emerald-600' : 'text-emerald-600' }}"></i>NOS BOUTIQUES
                    @if(request()->routeIs('marketplace.public.shops'))
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-500 rounded-full"></span>
                    @endif
                </a>
                <a href="{{ route('marketplace.public.vendre') }}"
                   class="relative font-bold hover:text-emerald-600 transition whitespace-nowrap pb-1 {{ request()->routeIs('marketplace.public.vendre') ? 'text-emerald-600' : 'text-gray-600' }}">
                    <i class="fas fa-plus-circle mr-2 {{ request()->routeIs('marketplace.public.vendre') ? 'text-emerald-600' : 'text-emerald-600' }}"></i>VENDRE
                    @if(request()->routeIs('marketplace.public.vendre'))
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-500 rounded-full"></span>
                    @endif
                </a>
                <a href="{{ route('marketplace.public.contact') }}"
                   class="relative font-bold hover:text-emerald-600 transition whitespace-nowrap pb-1 {{ request()->routeIs('marketplace.public.contact') ? 'text-emerald-600' : 'text-gray-600' }}">
                    <i class="fas fa-envelope mr-2 {{ request()->routeIs('marketplace.public.contact') ? 'text-emerald-600' : 'text-emerald-600' }}"></i>CONTACT
                    @if(request()->routeIs('marketplace.public.contact'))
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-500 rounded-full"></span>
                    @endif
                </a>
            </nav>

            {{-- Mobile --}}
            <div class="md:hidden flex items-center justify-between py-3 gap-4">
                <button @click="open = !open" class="focus:outline-none relative z-50 w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center text-white flex-shrink-0">
                    <i class="fas fa-bars text-lg" x-show="!open"></i>
                    <i class="fas fa-times text-lg" x-show="open"></i>
                </button>
                <div class="flex items-center gap-3 text-sm text-gray-700 whitespace-nowrap">
                    <span class="flex items-center gap-1"><i class="fas fa-phone text-emerald-500"></i> 77 260 79 77</span>
                    <span class="flex items-center gap-1"><i class="fas fa-map-marker-alt text-emerald-500"></i> Adresse : GUEULE TAPEE</span>
                </div>
            </div>
        </div>

        {{-- Overlay --}}
        <div x-cloak x-show="open" x-transition.opacity.duration.300ms @click="open = false" class="fixed inset-0 bg-black/50 z-40 md:hidden"></div>

        {{-- Panneau mobile gauche --}}
        <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed top-0 left-0 h-full w-72 bg-white shadow-2xl z-50 md:hidden overflow-y-auto">
            <div class="flex items-center justify-between p-4 border-b">
                <span class="text-gray-900 font-bold text-lg">MENU</span>
                <button @click="open = false" class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="p-4 space-y-1">
                <a href="{{ route('marketplace.public.home') }}" @click="open = false"
                   class="flex items-center py-3 px-4 rounded-lg transition {{ request()->routeIs('marketplace.public.home') ? 'text-emerald-600 font-bold bg-emerald-50' : 'text-gray-600 font-bold hover:bg-emerald-50 hover:text-emerald-600' }}">
                    <i class="fas fa-home w-6 mr-3"></i>ACCUEIL
                </a>
                <a href="{{ route('marketplace.public.all-products') }}" @click="open = false"
                   class="flex items-center py-3 px-4 rounded-lg transition {{ request()->routeIs('marketplace.public.all-products') ? 'text-emerald-600 font-bold bg-emerald-50' : 'text-gray-600 font-bold hover:bg-emerald-50 hover:text-emerald-600' }}">
                    <i class="fas fa-box w-6 mr-3"></i>TOUS LES PRODUITS
                </a>
                <a href="{{ route('marketplace.public.promotions') }}" @click="open = false"
                   class="flex items-center py-3 px-4 rounded-lg transition {{ request()->routeIs('marketplace.public.promotions') ? 'text-emerald-600 font-bold bg-emerald-50' : 'text-gray-600 font-bold hover:bg-emerald-50 hover:text-emerald-600' }}">
                    <i class="fas fa-tag w-6 mr-3"></i>PROMOTIONS
                </a>
                <a href="{{ route('marketplace.public.nouveautes') }}" @click="open = false"
                   class="flex items-center py-3 px-4 rounded-lg transition {{ request()->routeIs('marketplace.public.nouveautes') ? 'text-emerald-600 font-bold bg-emerald-50' : 'text-gray-600 font-bold hover:bg-emerald-50 hover:text-emerald-600' }}">
                    <i class="fas fa-star w-6 mr-3"></i>NOUVEAUTÉS
                </a>
                <a href="{{ route('marketplace.public.shops') }}" @click="open = false"
                   class="flex items-center py-3 px-4 rounded-lg transition {{ request()->routeIs('marketplace.public.shops') ? 'text-emerald-600 font-bold bg-emerald-50' : 'text-gray-600 font-bold hover:bg-emerald-50 hover:text-emerald-600' }}">
                    <i class="fas fa-store w-6 mr-3"></i>NOS BOUTIQUES
                </a>
                <a href="{{ route('marketplace.public.vendre') }}" @click="open = false"
                   class="flex items-center py-3 px-4 rounded-lg transition {{ request()->routeIs('marketplace.public.vendre') ? 'text-emerald-600 font-bold bg-emerald-50' : 'text-gray-600 font-bold hover:bg-emerald-50 hover:text-emerald-600' }}">
                    <i class="fas fa-plus-circle w-6 mr-3"></i>VENDRE
                </a>
                <a href="{{ route('marketplace.public.contact') }}" @click="open = false"
                   class="flex items-center py-3 px-4 rounded-lg transition {{ request()->routeIs('marketplace.public.contact') ? 'text-emerald-600 font-bold bg-emerald-50' : 'text-gray-600 font-bold hover:bg-emerald-50 hover:text-emerald-600' }}">
                    <i class="fas fa-envelope w-6 mr-3"></i>CONTACT
                </a>
            </div>
        </div>
    </div>
</header>

@yield('content')
<footer class="bg-gray-900 text-white mt-12">
    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            {{-- Colonne 1 --}}
            <div>
                <div class="flex items-center gap-2 mb-4 mt-6">
                    <img src="{{ asset('images/fatihads.png') }}" alt="FatiHad" class="w-8 h-auto rounded-lg object-cover -mt-2">
                    <h3 class="text-lg font-bold relative inline-block">
                        FatiHad
                        <span class="absolute -bottom-1 left-0 w-1/2 h-0.5 bg-emerald-500"></span>
                    </h3>
                </div>
                <p class="text-gray-400 text-sm mt-4">La marketplace de confiance pour acheter et vendre en toute sécurité.</p>
                <p class="text-gray-400 text-sm mt-4">La solution SaaS pour créer votre boutique en ligne au Sénégal.</p>
                <div class="flex items-center gap-3 mt-4">
                    <a href="#" class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center hover:bg-emerald-500 transition"><i class="fab fa-facebook-f text-sm"></i></a>
                    <a href="#" class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center hover:bg-emerald-500 transition"><i class="fab fa-instagram text-sm"></i></a>
                    <a href="#" class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center hover:bg-emerald-500 transition"><i class="fab fa-tiktok text-sm"></i></a>
                    <a href="#" class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center hover:bg-emerald-500 transition"><i class="fab fa-whatsapp text-sm"></i></a>
                </div>
            </div>

            {{-- Colonne 2 --}}
            <div>
                <h3 class="text-lg font-bold mb-4 mt-6 relative inline-block">
                    Liens utiles
                    <span class="absolute -bottom-1 left-0 w-1/2 h-0.5 bg-emerald-500"></span>
                </h3>
                <ul class="space-y-2 text-gray-400 text-sm mt-4">
                    <li><a href="#" class="hover:text-emerald-500 transition">Accueil</a></li>
                    <li><a href="#" class="hover:text-emerald-500 transition">Tous les produits</a></li>
                    <li><a href="#" class="hover:text-emerald-500 transition">Promotions</a></li>
                    <li><a href="#" class="hover:text-emerald-500 transition">Nouveautés</a></li>
                    <li><a href="#" class="hover:text-emerald-500 transition">Nos boutiques</a></li>
                </ul>
            </div>

            {{-- Colonne 3 --}}
            <div>
                <h3 class="text-lg font-bold mb-4 mt-6 relative inline-block">
                    Informations
                    <span class="absolute -bottom-1 left-0 w-1/2 h-0.5 bg-emerald-500"></span>
                </h3>
                <ul class="space-y-2 text-gray-400 text-sm mt-4">
                    <li><a href="#" class="hover:text-emerald-500 transition">Comment acheter</a></li>
                    <li><a href="#" class="hover:text-emerald-500 transition">Comment vendre</a></li>
                    <li><a href="#" class="hover:text-emerald-500 transition">Conditions d'utilisation</a></li>
                    <li><a href="#" class="hover:text-emerald-500 transition">Politique de confidentialité</a></li>
                    <li><a href="#" class="hover:text-emerald-500 transition">FAQ</a></li>
                </ul>
            </div>

            {{-- Colonne 4 --}}
            <div>
                <h3 class="text-lg font-bold mb-4 mt-6 relative inline-block">
                    Contact
                    <span class="absolute -bottom-1 left-0 w-1/2 h-0.5 bg-emerald-500"></span>
                </h3>
                <ul class="space-y-2 text-gray-400 text-sm mt-4">
                    <li class="flex items-center gap-2"><i class="fas fa-phone text-emerald-500 w-4"></i> 77 137 39 39</li>
                    <li class="flex items-center gap-2"><i class="fas fa-envelope text-emerald-500 w-4"></i> contact@seneshop.sn</li>
                    <li class="flex items-center gap-2"><i class="fas fa-map-marker-alt text-emerald-500 w-4"></i> DAKAR, GUEULE TAPEE</li>
                </ul>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="border-t border-gray-800 mt-8 pt-6 text-center text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} Marketplace - Tous droits réservés.</p>
        </div>
    </div>
</footer>
@stack('scripts')
{{-- PWA Service Worker --}}
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js').then(function(registration) {
                console.log('Service Worker enregistré avec succès:', registration.scope);
            }, function(err) {
                console.log('Échec de l\'enregistrement du Service Worker:', err);
            });
        });
    }
</script>
</body>
</body>
</html>
