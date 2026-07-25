@extends('layouts.marketplace')

@section('title', 'Vendre sur FatiHad - Seneshop')

@push('styles')
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { scrollbar-width: none; -ms-overflow-style: none; }
    </style>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-6">
        {{-- Fil d'ariane --}}
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('marketplace.public.home') }}" class="hover:text-emerald-600 transition">Accueil</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-900 font-medium">Vendre sur FatiHad</span>
        </nav>

        {{-- HERO SECTION --}}
        <div class="relative rounded-2xl overflow-hidden shadow-lg mb-12">
            {{-- Desktop --}}
            <img src="{{ asset('images/cta-ban.png') }}" alt="Vendre sur Seneshop" class="hidden md:block w-full h-auto object-contain">
            {{-- Mobile --}}
            <img src="{{ asset('images/vendre-banner-mobiles.png') }}" alt="Vendre sur Seneshop" class="block md:hidden w-full h-auto object-contain">
        </div>

        {{-- LES 4 AVANTAGES --}}
        <div id="avantages" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-16">
            <div class="bg-white rounded-xl shadow-sm p-6 text-center hover:shadow-md transition border border-gray-100">
                <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-plus text-2xl text-emerald-600"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">Inscription simple</h3>
                <p class="text-sm text-gray-500">Créez votre compte en quelques minutes</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 text-center hover:shadow-md transition border border-gray-100">
                <div class="w-14 h-14 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-eye text-2xl text-indigo-600"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">Plus de visibilité</h3>
                <p class="text-sm text-gray-500">Touchez plus de clients facilement</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 text-center hover:shadow-md transition border border-gray-100">
                <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-cogs text-2xl text-orange-600"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">Gestion facile</h3>
                <p class="text-sm text-gray-500">Gérez vos produits simplement</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 text-center hover:shadow-md transition border border-gray-100">
                <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-2xl text-green-600"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">Cadre sécurisé</h3>
                <p class="text-sm text-gray-500">Vendez en toute confiance</p>
            </div>
        </div>

        {{-- POURQUOI VENDRE SUR XALA --}}
        <div class="mb-16">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 text-center mb-3">
                Pourquoi vendre <span class="text-emerald-500">sur FatiHad Sénégal</span> ?
            </h2>
            <p class="text-gray-500 text-center mb-10"></p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- 01 --}}
                <div class="bg-white rounded-xl shadow-sm p-6 flex gap-4 hover:shadow-md transition border border-gray-100">
                    <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-emerald-600 font-bold text-lg">01</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">Boutique prête</h3>
                        <p class="text-sm text-gray-500">Créez votre espace vendeur, ajoutez vos produits et présentez votre boutique simplement.</p>
                    </div>
                </div>

                {{-- 02 --}}
                <div class="bg-white rounded-xl shadow-sm p-6 flex gap-4 hover:shadow-md transition border border-gray-100">
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-indigo-600 font-bold text-lg">02</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">Plus visible</h3>
                        <p class="text-sm text-gray-500">Touchez des clients partout au Sénégal grâce à une plateforme pensée pour le marché local.</p>
                    </div>
                </div>

                {{-- 03 --}}
                <div class="bg-white rounded-xl shadow-sm p-6 flex gap-4 hover:shadow-md transition border border-gray-100">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-green-600 font-bold text-lg">03</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">Paiements simplifiés</h3>
                        <p class="text-sm text-gray-500">Recevez vos paiements directement. Pas de frais cachés, pas de surprises, rassurant pour vos clients.</p>
                    </div>
                </div>

                {{-- 04 --}}
                <div class="bg-white rounded-xl shadow-sm p-6 flex gap-4 hover:shadow-md transition border border-gray-100">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-orange-600 font-bold text-lg">04</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">Accompagnement humain</h3>
                        <p class="text-sm text-gray-500">Une équipe basée à Dakar, disponible par téléphone et WhatsApp pour vous aider.</p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-8 py-4 rounded-full transition shadow-lg">
                    <i class="fas fa-store"></i> Créer ma boutique
                </a>
            </div>
        </div>

        {{-- COMMENT VENDRE --}}
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-8 md:p-12 mb-16">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 text-center mb-3">
                Comment vendre <span class="text-emerald-500">sur FatiHad Sénégal</span> ?
            </h2>
            <p class="text-gray-500 text-center mb-10"></p>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                {{-- 01 --}}
                <div class="text-center">
                    <div class="w-16 h-16 bg-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <span class="text-white font-bold text-xl">01</span>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Inscrivez-vous</h3>
                    <p class="text-sm text-gray-500">Créez votre compte vendeur en 2 minutes. C'est gratuit et sans engagement.</p>
                </div>

                {{-- 02 --}}
                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <span class="text-white font-bold text-xl">02</span>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Préparez votre boutique</h3>
                    <p class="text-sm text-gray-500">Ajoutez votre logo, votre description, vos horaires. Votre boutique vous ressemble.</p>
                </div>

                {{-- 03 --}}
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <span class="text-white font-bold text-xl">03</span>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Publiez vos produits</h3>
                    <p class="text-sm text-gray-500">Ajoutez vos photos, prix et descriptions pour présenter vos articles proprement sur notre marketplace.</p>
                </div>

                {{-- 04 --}}
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <span class="text-white font-bold text-xl">04</span>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Commencez à vendre</h3>
                    <p class="text-sm text-gray-500">Recevez des commandes, gérez vos ventes et suivez vos revenus depuis votre tableau de bord.</p>
                </div>
            </div>

            <div class="text-center mt-10">
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-8 py-4 rounded-full transition shadow-lg">
                    Commencer maintenant
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        {{-- QUE GAGNEZ-VOUS --}}
        <div class="mb-16">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 text-center mb-3">
                Que gagnez-vous <span class="text-emerald-500">avec FatiHad Sénégal</span> ?
            </h2>
            <p class="text-gray-500 text-center mb-10"></p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- 01 --}}
                <div class="bg-white rounded-xl shadow-sm p-6 flex gap-4 hover:shadow-md transition border border-gray-100">
                    <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-emerald-600 font-bold text-lg">01</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">Votre propre vitrine</h3>
                        <p class="text-sm text-gray-500">Une page boutique personnalisée avec tous vos produits, vos infos et vos avis clients.</p>
                    </div>
                </div>

                {{-- 02 --}}
                <div class="bg-white rounded-xl shadow-sm p-6 flex gap-4 hover:shadow-md transition border border-gray-100">
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-indigo-600 font-bold text-lg">02</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">Des clients qualifiés</h3>
                        <p class="text-sm text-gray-500">Des acheteurs prêts à commander, qui cherchent des produits comme les vôtres.</p>
                    </div>
                </div>

                {{-- 03 --}}
                <div class="bg-white rounded-xl shadow-sm p-6 flex gap-4 hover:shadow-md transition border border-gray-100">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-orange-600 font-bold text-lg">03</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">Une gestion tout-en-un</h3>
                        <p class="text-sm text-gray-500">Stocks, commandes, messages clients : tout se gère depuis un seul espace.</p>
                    </div>
                </div>

                {{-- 04 --}}
                <div class="bg-white rounded-xl shadow-sm p-6 flex gap-4 hover:shadow-md transition border border-gray-100">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-green-600 font-bold text-lg">04</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">Des statistiques claires</h3>
                        <p class="text-sm text-gray-500">Visualisez vos ventes, vos produits les plus consultés et vos revenus en temps réel.</p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-8 py-4 rounded-full transition shadow-lg">
                    <i class="fas fa-store"></i> Créer ma boutique
                </a>
            </div>
        </div>

        {{-- QUE VENDRE --}}
        <div class="bg-white rounded-2xl shadow-sm p-8 md:p-12 mb-16 border border-gray-100 text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                Que vendre sur <span class="text-emerald-500">FatiHad Sénégal</span> ?
            </h2>
            <div class="max-w-3xl mx-auto">
                <p class="text-gray-600 mb-6 leading-relaxed text-lg">
                    Mode, beauté, high-tech, maison, alimentation, artisanat, services…
                </p>
                <p class="text-gray-900 font-bold text-xl mb-6">
                    Si c'est de qualité, ça a sa place sur Seneshop.
                </p>
                <p class="text-gray-500 leading-relaxed">
                    Que vous soyez un petit commerçant, un artisan ou une marque établie, Seneshop vous donne les outils pour vendre en ligne simplement, sans investir dans un site web coûteux.
                </p>
            </div>
        </div>

        {{-- CTA FINAL --}}
        <div class="relative rounded-2xl overflow-hidden shadow-lg mb-12">
            {{-- Desktop --}}
            <img src="{{ asset('images/vendre-bannerr.png') }}" alt="Prêt à vendre sur Seneshop" class="hidden md:block w-full h-auto object-contain">
            {{-- Mobile --}}
            <img src="{{ asset('images/cta-banner-mobile.png') }}" alt="Prêt à vendre sur Seneshop" class="block md:hidden w-full h-auto object-contain">
        </div>
    </div>
@endsection
