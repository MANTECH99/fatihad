{{-- resources/views/layouts/storefront.blade.php --}}
    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $shop->name ?? 'Boutique') - Fatihad</title>

    {{-- PWA Manifest dynamique --}}
    <link rel="manifest" href="{{ isset($shop) ? route('storefront.manifest', $shop->slug) : asset('manifest-storefront.json') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ $shop->name ?? 'Seneshop' }}">
    <link rel="apple-touch-icon" href="{{ $shop->logo_url ?? asset('images/icons/icon-192x192.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="theme-color" content="#10B981">
    @stack('styles')

    <style>
        .overflow-x-auto::-webkit-scrollbar { display: none; }
        .overflow-x-auto { -ms-overflow-style: none; scrollbar-width: none; }

        .search-input {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            -webkit-appearance: none;
            appearance: none;
            background: transparent;
        }

        .search-input:focus {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
        }
    </style>

    @if($shop->facebook_pixel_id)
        <!-- Meta Pixel Code -->
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window, document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $shop->facebook_pixel_id }}');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
                       src="https://www.facebook.com/tr?id={{ $shop->facebook_pixel_id }}&ev=PageView&noscript=1"
            /></noscript>
        <!-- End Meta Pixel Code -->
    @endif


    <script src="https://dashboard.causeriebot.com/widget.js" data-id="5924dd18-3bb4-4ee5-b684-34dddf8bb1f0" data-url="https://dashboard.causeriebot.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

<header class="bg-white shadow-sm sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-3">
        {{-- Logo + Nom --}}
        @if(isset($shop) && $shop->logo_url)
            <img src="{{ $shop->logo_url }}" alt="{{ $shop->name }}" class="h-10 w-10 rounded-full object-cover flex-shrink-0">
        @endif
        <a href="{{ isset($shop) ? route('storefront.show', $shop->slug) : url('/') }}" class="font-bold text-gray-900 text-base truncate max-w-[120px] flex-shrink-0 hidden sm:block">
            {{ $shop->name ?? 'Seneshop' }}
        </a>

        {{-- Barre de recherche --}}
        <form action="{{ isset($shop) ? route('storefront.show', $shop->slug) : '#' }}" method="GET" class="flex-1 min-w-0">
            <div class="flex items-center h-11 bg-white border border-gray-300 rounded-lg overflow-hidden shadow-sm">

                {{-- Icône --}}
                <div class="flex items-center justify-center px-3 text-gray-400">
                    <i class="fas fa-search text-base"></i>
                </div>

                <input
                    type="text"
                    name="q"
                    placeholder="Cherchez un produit, une marque ou une catégorie"
                    autocomplete="off"
                    class="search-input flex-1 w-full h-full px-2 text-sm text-gray-700 placeholder-gray-400">
                <button
                    type="submit"
                    class="hidden sm:flex h-full px-6 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium border-0 outline-none focus:outline-none focus:ring-0 transition items-center">
                    Rechercher
                </button>

            </div>
        </form>

        {{-- ✅ AJOUT ICI : Senegal et Vendre APRÈS le formulaire de recherche --}}
        <div class="hidden sm:flex items-center gap-2 flex-shrink-0">
            {{-- Bouton Senegal (Bordure orange) --}}
            <a href="#" class="flex items-center gap-1.5 px-3 py-1.5 border-2 border-orange-500 text-black-500 text-sm font-medium rounded-full bg-transparent hover:bg-orange-50 transition whitespace-nowrap">
                <i class="fas fa-map-marker-alt text-xs"></i> Senegal
            </a>

            {{-- Bouton Vendre (Fond orange) --}}
            <a href="{{ route('register') }}" class="flex items-center gap-1.5 px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-full transition whitespace-nowrap">
                <i class="fas fa-plus-circle"></i> Vendre
            </a>
        </div>

        {{-- Panier --}}
        @if(isset($shop))
            <a href="{{ route('storefront.checkout', $shop->slug) }}" class="relative text-emerald-500 flex-shrink-0">
                <i class="fas fa-shopping-cart text-xl"></i>
                <span id="cart-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center" style="display:none">0</span>
            </a>
        @endif
    </div>
</header>

{{-- Bouton d'installation PWA --}}
<div id="pwa-install-banner" class="hidden bg-emerald-500 text-white max-w-7xl mx-auto px-4 py-3 flex items-center justify-between shadow-md">
    <div class="flex items-center gap-3">
        @if(isset($shop) && $shop->logo_url)
            <img src="{{ $shop->logo_url }}" alt="{{ $shop->name }}" class="w-8 h-8 rounded-full">
        @endif
        <div>
            <p class="font-bold text-sm">{{ $shop->name ?? 'Seneshop' }}</p>
            <p class="text-xs text-white/80">Installez l'app sur votre écran d'accueil</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <button id="pwa-install-btn" class="bg-white text-emerald-600 font-bold px-4 py-1.5 rounded-full text-sm hover:bg-gray-100 transition">
            Installer
        </button>
        <button id="pwa-dismiss-btn" class="text-white/80 hover:text-white">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

@yield('content')

@stack('scripts')
<script>
    const SHOP_SLUG = '{{ $shop->slug ?? "" }}';
    const CART_ADD_URL = '{{ isset($shop) ? secure_url("cart/{$shop->slug}/add") : "" }}';
    const CART_GET_URL = '{{ isset($shop) ? secure_url("cart/{$shop->slug}") : "" }}';
    const CART_REMOVE_URL = '{{ isset($shop) ? secure_url("cart/{$shop->slug}/remove") : "" }}';
    if (CART_GET_URL) {
        fetch(CART_GET_URL).then(r => r.json()).then(d => {
            if (d.count > 0) {
                const b = document.getElementById('cart-badge');
                b.textContent = d.count;
                b.style.display = 'flex';
            }
        });
    }
</script>

{{-- PWA Service Worker + Installation --}}
<script>
    let deferredPrompt;

    // Enregistrer le Service Worker CLIENT
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                // Désenregistrer les anciens SW
                registrations.forEach(function(registration) {
                    if (registration.scope.includes('/shop/') === false) {
                        registration.unregister();
                    }
                });
            }).then(function() {
                navigator.serviceWorker.register('/sw-storefront-client.js', { scope: '/shop/' })
                    .then(function(reg) {
                        console.log('SW Client enregistré:', reg.scope);
                    })
                    .catch(function(err) {
                        console.log('SW Client échec:', err);
                    });
            });
        });
    }

    // Intercepter beforeinstallprompt
    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        deferredPrompt = e;
        const banner = document.getElementById('pwa-install-banner');
        if (banner) banner.classList.remove('hidden');
    });

    // Boutons
    document.addEventListener('DOMContentLoaded', function() {
        const installBtn = document.getElementById('pwa-install-btn');
        const dismissBtn = document.getElementById('pwa-dismiss-btn');
        const banner = document.getElementById('pwa-install-banner');

        if (installBtn) {
            installBtn.addEventListener('click', async function() {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const result = await deferredPrompt.userChoice;
                    console.log('Installation:', result.outcome);
                    deferredPrompt = null;
                    banner.classList.add('hidden');
                }
            });
        }

        if (dismissBtn) {
            dismissBtn.addEventListener('click', function() {
                banner.classList.add('hidden');
            });
        }
    });

    window.addEventListener('appinstalled', function() {
        const banner = document.getElementById('pwa-install-banner');
        if (banner) banner.classList.add('hidden');
    });
</script>
</body>
</html>
