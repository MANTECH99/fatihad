{{-- resources/views/landing/home.blade.php --}}
    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FatiHad - Créez votre boutique en ligne en 5 minutes | SaaS Sénégal</title>

    {{-- Tailwind via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#10B981',
                        secondary: '#1F2937',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- AOS Animation Library --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <meta name="theme-color" content="#10B981">
    <meta name="description" content="FatiHad - La solution SaaS pour créer et gérer votre boutique en ligne. Catalogues, commandes WhatsApp, paiements Wave et Orange Money. Plans adaptés à tous les budgets.">

    <style>

        /* Masquer la barre de défilement pour Chrome, Safari, Edge */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        /* Masquer la barre de défilement pour Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;     /* Firefox */
        }

        /* Masquer la barre de défilement pour tous les navigateurs */
        .scrollbar-hide {
            -ms-overflow-style: none;  /* Internet Explorer et Edge */
            scrollbar-width: none;     /* Firefox */
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;             /* Chrome, Safari et Opera */
        }
        * { font-family: 'Inter', sans-serif; }
        html { scroll-behavior: smooth; }

        [x-cloak] { display: none !important; }

        .gradient-hero {
            background: linear-gradient(135deg, #10B981 0%, #059669 50%, #047857 100%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .gradient-text {
            background: linear-gradient(135deg, #10B981, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .pricing-card {
            transition: all 0.3s ease;
        }

        .pricing-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(16, 185, 129, 0.15);
        }

        .feature-icon {
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(3deg);
            color: #059669;
        }

        .testimonial-card {
            transition: all 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.4s ease;
        }

        .faq-item.active .faq-answer {
            max-height: 300px;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .faq-item.active .faq-icon {
            transform: rotate(45deg);
        }

        .faq-icon {
            transition: transform 0.3s ease;
        }

        /* Empêcher le scroll quand le menu mobile est ouvert */
        body.menu-open {
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-white">

<!-- ==================== NAVBAR ==================== -->
<nav x-data="{
    mobileOpen: false,
    scrolled: false
}"
     @scroll.window="scrolled = window.scrollY > 50"
     @mobileopen.watch="document.body.classList.toggle('menu-open', mobileOpen)"
     class="fixed w-full z-50 transition-all duration-300"
     :class="scrolled ? 'bg-white shadow-lg' : 'bg-transparent'">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 lg:h-20">

            <!-- Logo -->
            <a href="#" class="flex items-center gap-2 flex-shrink-0">
                <img src="{{ asset('images/fatihad.png') }}" alt="FatiHad" class="h-10 w-auto -mt-3">
                <span class="text-2xl font-bold text-gray-900">FatiHad</span>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center gap-8">
                <a href="#fonctionnalites" class="font-medium transition-colors duration-300 text-gray-700 hover:text-primary">Fonctionnalités</a>
                <a href="#tarifs" class="font-medium transition-colors duration-300 text-gray-700 hover:text-primary">Tarifs</a>
                <a href="#temoignages" class="font-medium transition-colors duration-300 text-gray-700 hover:text-primary">Témoignages</a>
                <a href="#faq" class="font-medium transition-colors duration-300 text-gray-700 hover:text-primary">FAQ</a>
                <a href="#contact" class="font-medium transition-colors duration-300 text-gray-700 hover:text-primary">Contact</a>
            </div>

            <!-- Desktop CTA -->
            <div class="hidden lg:flex items-center gap-4">
                <a href="{{ route('login') }}" class="font-medium transition-colors duration-300 text-gray-700 hover:text-primary">Connexion</a>
                <a href="{{ route('register') }}" class="bg-primary text-white font-semibold px-6 py-2.5 rounded-full hover:shadow-lg transition-all duration-300 transform hover:scale-105 whitespace-nowrap">
                    Essai gratuit
                </a>
            </div>

            <!-- Mobile Hamburger -->
            <button @click="mobileOpen = !mobileOpen" class="lg:hidden relative z-50 p-2" aria-label="Menu">
                <i class="fas text-2xl transition-colors duration-300 text-gray-900" :class="mobileOpen ? 'fa-times' : 'fa-bars'"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div
        x-show="mobileOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-white z-40 overflow-y-auto"
        style="top: 0;"
    >
        <div class="flex flex-col items-center justify-center min-h-screen gap-6 text-lg font-medium px-4">
            <a href="#fonctionnalites" @click="mobileOpen = false" class="text-gray-700 hover:text-primary transition-colors w-full text-center py-3">Fonctionnalités</a>
            <a href="#tarifs" @click="mobileOpen = false" class="text-gray-700 hover:text-primary transition-colors w-full text-center py-3">Tarifs</a>
            <a href="#temoignages" @click="mobileOpen = false" class="text-gray-700 hover:text-primary transition-colors w-full text-center py-3">Témoignages</a>
            <a href="#faq" @click="mobileOpen = false" class="text-gray-700 hover:text-primary transition-colors w-full text-center py-3">FAQ</a>
            <a href="#contact" @click="mobileOpen = false" class="text-gray-700 hover:text-primary transition-colors w-full text-center py-3">Contact</a>

            <div class="flex flex-col gap-3 w-64 mt-8">
                <a href="{{ route('login') }}" class="text-center border-2 border-primary text-primary font-semibold py-3 rounded-full hover:bg-primary hover:text-white transition-colors">
                    Connexion
                </a>
                <a href="{{ route('register') }}" class="text-center bg-primary text-white font-semibold py-3 rounded-full hover:bg-emerald-600 transition-colors">
                    Essai gratuit
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- ==================== HERO SECTION ==================== -->
<section class="bg-white min-h-screen flex items-center pt-12 lg:pt-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-32 w-full">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Texte -->
            <div data-aos="fade-right" data-aos-duration="800">
                <div class="inline-flex items-center bg-primary/10 backdrop-blur-sm px-4 py-2 rounded-full mb-6">
                    <span class="w-2 h-2 bg-primary rounded-full animate-pulse mr-2"></span>
                    <span class="text-primary text-sm font-medium">Lancé au Sénégal 🇸🇳</span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-gray-900 leading-tight mb-6">
                    Votre boutique en ligne
                    <span class="text-primary" x-data="typedText()" x-init="init()">
        <span x-text="displayText" class="inline-block"></span>
        <span class="inline-block w-1 h-[1em] bg-primary animate-pulse align-middle ml-0.5"></span>
    </span>
                </h1>

                <p class="text-base sm:text-lg lg:text-xl text-gray-600 mb-8 leading-relaxed">
                    Créez votre catalogue, recevez des commandes WhatsApp, encaissez avec <strong>Wave</strong> et <strong>Orange Money</strong>.
                    Sans compétence technique. Sans commission sur vos ventes.
                </p>

                {{-- Avantages rapides --}}
                <div class="grid grid-cols-2 gap-3 mb-8">
                    <div class="flex items-center gap-2 text-gray-700 bg-gray-50 rounded-lg px-3 py-2 border border-gray-200">
                        <i class="fas fa-check-circle text-primary text-sm"></i>
                        <span class="text-sm">Catalogue illimité</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-700 bg-gray-50 rounded-lg px-3 py-2 border border-gray-200">
                        <i class="fas fa-check-circle text-primary text-sm"></i>
                        <span class="text-sm">Paiement mobile</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-700 bg-gray-50 rounded-lg px-3 py-2 border border-gray-200">
                        <i class="fas fa-check-circle text-primary text-sm"></i>
                        <span class="text-sm">WhatsApp intégré</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-700 bg-gray-50 rounded-lg px-3 py-2 border border-gray-200">
                        <i class="fas fa-check-circle text-primary text-sm"></i>
                        <span class="text-sm">14 jours d'essai</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 mb-8">
                    <a href="{{ route('register') }}" class="bg-primary text-white font-bold text-lg px-8 py-4 rounded-full text-center hover:shadow-xl hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2">
                        Commencer gratuitement
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('marketplace.public.home') }}" class="border-2 border-primary text-primary font-semibold text-lg px-8 py-4 rounded-full text-center hover:bg-primary hover:text-white transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="fas fa-play"></i>
                        Marketplace
                    </a>
                </div>

                <div class="flex items-center gap-6 text-gray-500">
                    <div class="flex -space-x-3">
                        <div class="w-10 h-10 rounded-full bg-primary/20 border-2 border-white flex items-center justify-center text-xs font-bold text-primary">JD</div>
                        <div class="w-10 h-10 rounded-full bg-primary/20 border-2 border-white flex items-center justify-center text-xs font-bold text-primary">AM</div>
                        <div class="w-10 h-10 rounded-full bg-primary/20 border-2 border-white flex items-center justify-center text-xs font-bold text-primary">FS</div>
                        <div class="w-10 h-10 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-xs font-bold text-gray-500">+</div>
                    </div>
                    <span class="text-sm"><strong class="text-gray-900">+500</strong> boutiques créées • <strong class="text-gray-900">98%</strong> de satisfaction</span>
                </div>
            </div>

            <!-- Image avec carrousel -->
            <div data-aos="fade-left" data-aos-duration="800" class="relative hidden lg:block" x-data="{ currentSlide: 0 }" x-init="setInterval(() => { currentSlide = (currentSlide + 1) % 5 }, 3000)">
                <div class="relative mx-auto w-full max-w-md">
                    <!-- Images -->
                    <div class="relative overflow-hidden rounded-2xl">
                        <img src="{{ asset('images/hero-image.png') }}" alt="FatiHad" class="w-full h-auto rounded-2xl transition-opacity duration-700" :class="currentSlide === 0 ? 'opacity-100' : 'opacity-0 absolute inset-0'">
                        <img src="{{ asset('images/hero-image1.png') }}" alt="FatiHad" class="w-full h-auto rounded-2xl transition-opacity duration-700" :class="currentSlide === 1 ? 'opacity-100' : 'opacity-0 absolute inset-0'">
                        <img src="{{ asset('images/hero-image2.png') }}" alt="FatiHad" class="w-full h-auto rounded-2xl transition-opacity duration-700" :class="currentSlide === 2 ? 'opacity-100' : 'opacity-0 absolute inset-0'">
                        <img src="{{ asset('images/hero-image3.png') }}" alt="FatiHad" class="w-full h-auto rounded-2xl transition-opacity duration-700" :class="currentSlide === 3 ? 'opacity-100' : 'opacity-0 absolute inset-0'">
                        <img src="{{ asset('images/hero-image5.png') }}" alt="FatiHad" class="w-full h-auto rounded-2xl transition-opacity duration-700" :class="currentSlide === 4 ? 'opacity-100' : 'opacity-0 absolute inset-0'">
                    </div>

                    <!-- Indicateurs en dessous -->
                    <div class="flex justify-center gap-2 mt-4">
                        <button @click="currentSlide = 0" :class="currentSlide === 0 ? 'bg-primary w-6' : 'bg-gray-300 w-2'" class="h-2 rounded-full transition-all duration-300"></button>
                        <button @click="currentSlide = 1" :class="currentSlide === 1 ? 'bg-primary w-6' : 'bg-gray-300 w-2'" class="h-2 rounded-full transition-all duration-300"></button>
                        <button @click="currentSlide = 2" :class="currentSlide === 2 ? 'bg-primary w-6' : 'bg-gray-300 w-2'" class="h-2 rounded-full transition-all duration-300"></button>
                        <button @click="currentSlide = 3" :class="currentSlide === 3 ? 'bg-primary w-6' : 'bg-gray-300 w-2'" class="h-2 rounded-full transition-all duration-300"></button>
                        <button @click="currentSlide = 4" :class="currentSlide === 4 ? 'bg-primary w-6' : 'bg-gray-300 w-2'" class="h-2 rounded-full transition-all duration-300"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ==================== FEATURES ==================== -->
<section id="fonctionnalites" class="pt-4 lg:pt-0 pb-20 lg:pb-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-primary font-semibold text-sm uppercase tracking-wider bg-primary/10 px-4 py-1.5 rounded-full">Tout ce dont vous avez besoin</span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 mt-4 mb-4">
                Lancez votre boutique
                <span class="gradient-text">sans stress</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">
                FatiHad gère la technique, vous gérez votre business. Concentrez-vous sur ce qui compte vraiment.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            <!-- Feature 1 -->
            <div class="feature-card bg-white border border-gray-100 rounded-2xl p-6 lg:p-8 hover:shadow-xl transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-icon w-12 h-12 lg:w-14 lg:h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-4 lg:mb-5">
                    <i class="fas fa-boxes text-xl lg:text-2xl text-primary"></i>
                </div>
                <h3 class="text-lg lg:text-xl font-bold text-gray-900 mb-2 lg:mb-3">Catalogue illimité</h3>
                <p class="text-sm lg:text-base text-gray-600">Ajoutez tous vos produits avec photos, prix et descriptions. Organisez-les en catégories pour une navigation facile.</p>
            </div>

            <!-- Feature 2 -->
            <div class="feature-card bg-white border border-gray-100 rounded-2xl p-6 lg:p-8 hover:shadow-xl transition-all duration-300" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-icon w-12 h-12 lg:w-14 lg:h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-4 lg:mb-5">
                    <i class="fab fa-whatsapp text-xl lg:text-2xl text-blue-600"></i>
                </div>
                <h3 class="text-lg lg:text-xl font-bold text-gray-900 mb-2 lg:mb-3">Commandes WhatsApp</h3>
                <p class="text-sm lg:text-base text-gray-600">Vos clients passent commande directement via WhatsApp. Recevez les détails instantanément, sans application.</p>
            </div>

            <!-- Feature 3 -->
            <div class="feature-card bg-white border border-gray-100 rounded-2xl p-6 lg:p-8 hover:shadow-xl transition-all duration-300" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-icon w-12 h-12 lg:w-14 lg:h-14 bg-orange-100 rounded-xl flex items-center justify-center mb-4 lg:mb-5">
                    <i class="fas fa-money-bill-wave text-xl lg:text-2xl text-orange-600"></i>
                </div>
                <h3 class="text-lg lg:text-xl font-bold text-gray-900 mb-2 lg:mb-3">Paiements mobiles</h3>
                <p class="text-sm lg:text-base text-gray-600">Acceptez Wave et Orange Money. Vos clients paient en un clic. Pas de terminal, pas de complication.</p>
            </div>

            <!-- Feature 4 -->
            <div class="feature-card bg-white border border-gray-100 rounded-2xl p-6 lg:p-8 hover:shadow-xl transition-all duration-300" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-icon w-12 h-12 lg:w-14 lg:h-14 bg-purple-100 rounded-xl flex items-center justify-center mb-4 lg:mb-5">
                    <i class="fas fa-chart-line text-xl lg:text-2xl text-purple-600"></i>
                </div>
                <h3 class="text-lg lg:text-xl font-bold text-gray-900 mb-2 lg:mb-3">Statistiques simples</h3>
                <p class="text-sm lg:text-base text-gray-600">Suivez vos ventes, vos produits populaires et la croissance de votre boutique avec des tableaux clairs.</p>
            </div>

            <!-- Feature 5 -->
            <div class="feature-card bg-white border border-gray-100 rounded-2xl p-6 lg:p-8 hover:shadow-xl transition-all duration-300" data-aos="fade-up" data-aos-delay="500">
                <div class="feature-icon w-12 h-12 lg:w-14 lg:h-14 bg-pink-100 rounded-xl flex items-center justify-center mb-4 lg:mb-5">
                    <i class="fas fa-globe text-xl lg:text-2xl text-pink-600"></i>
                </div>
                <h3 class="text-lg lg:text-xl font-bold text-gray-900 mb-2 lg:mb-3">Lien personnalisé</h3>
                <p class="text-sm lg:text-base text-gray-600">Partagez <strong>FatiHad.com/votreboutique</strong> sur vos réseaux sociaux. Vos clients commandent 24h/24.</p>
            </div>

            <!-- Feature 6 -->
            <div class="feature-card bg-white border border-gray-100 rounded-2xl p-6 lg:p-8 hover:shadow-xl transition-all duration-300" data-aos="fade-up" data-aos-delay="600">
                <div class="feature-icon w-12 h-12 lg:w-14 lg:h-14 bg-green-100 rounded-xl flex items-center justify-center mb-4 lg:mb-5">
                    <i class="fas fa-headset text-xl lg:text-2xl text-green-600"></i>
                </div>
                <h3 class="text-lg lg:text-xl font-bold text-gray-900 mb-2 lg:mb-3">Support réactif</h3>
                <p class="text-sm lg:text-base text-gray-600">Une équipe basée à Dakar, disponible par WhatsApp et téléphone pour vous accompagner à chaque étape.</p>
            </div>
        </div>
    </div>
</section>

<!-- ==================== PRICING ==================== -->
<section id="tarifs" class="pt-4 lg:pt-0 pb-20 lg:pb-32 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-primary font-semibold text-sm uppercase tracking-wider bg-primary/10 px-4 py-1.5 rounded-full">Des tarifs adaptés</span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 mt-4 mb-4">
                Choisissez votre
                <span class="gradient-text">plan</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">
                Commencez gratuitement. Passez à la vitesse supérieure quand vous êtes prêt. Sans engagement.
            </p>
        </div>

        <!-- Toggle -->
        <div class="flex items-center justify-center gap-4 mb-12">
            <span class="text-gray-700 font-medium text-sm sm:text-base" id="monthly-label">Mensuel</span>
            <button id="pricing-toggle" class="relative w-14 h-7 bg-gray-300 rounded-full transition-colors duration-300 focus:outline-none flex-shrink-0">
                <span class="absolute left-1 top-1 w-5 h-5 bg-white rounded-full shadow transition-transform duration-300"></span>
            </button>
            <span class="text-gray-700 font-medium text-sm sm:text-base" id="yearly-label">
                Annuel
                <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full ml-1">-20%</span>
            </span>
        </div>

        <!-- Pricing Cards -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
            <!-- Plan Gratuit -->
            <div class="pricing-card bg-white rounded-3xl border-2 border-gray-300 p-6 lg:p-8 transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl mb-4 mx-auto">
                    <i class="fas fa-seedling text-gray-800 text-xl"></i>
                </div>
                <div class="text-center">
                    <span class="inline-block bg-gray-100 text-gray-700 text-xs sm:text-sm font-semibold px-3 sm:px-4 py-1.5 rounded-full mb-4">GRATUIT</span>
                    <div class="mb-6">
                        <span class="text-4xl sm:text-4xl font-black text-gray-900">0</span>
                        <span class="text-gray-500 text-sm sm:text-base"> FCFA/mois</span>
                    </div>
                    <p class="text-gray-600 mb-6 text-sm">Pour démarrer</p>
                    <a href="{{ route('register') }}" class="block w-full bg-gray-200 text-gray-700 font-semibold py-3 rounded-xl hover:bg-gray-200 transition text-sm sm:text-base">
                        Essai gratuit 14 jours
                    </a>
                </div>
                <ul class="mt-6 lg:mt-8 space-y-2 lg:space-y-3">
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>1 Boutique en ligne</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Jusqu'à <strong>10 produits</strong></span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Lien personnalisé</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Commandes WhatsApp</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-400">
                        <i class="fas fa-times text-gray-400 mt-0.5 flex-shrink-0"></i>
                        <span>Paiements Wave/OM</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-400">
                        <i class="fas fa-times text-gray-400 mt-0.5 flex-shrink-0"></i>
                        <span>Statistiques avancées</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-400">
                        <i class="fas fa-times text-gray-400 mt-0.5 flex-shrink-0"></i>
                        <span>Pas de campagnes PUB</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-400">
                        <i class="fas fa-times text-gray-400 mt-0.5 flex-shrink-0"></i>
                        <span>Accompagnement personnel</span>
                    </li>
                </ul>
            </div>

            <!-- Plan Starter -->
            <div class="pricing-card bg-white rounded-3xl border-2 border-primary/30 p-6 lg:p-8 transition-all duration-300 relative" data-aos="fade-up" data-aos-delay="200">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-primary text-white text-xs font-bold px-4 py-1 rounded-full whitespace-nowrap">POPULAIRE</div>
                <!-- ICON PRO -->
                <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-xl mb-4 mx-auto">
                    <i class="fas fa-store text-green-600 text-xl"></i>
                </div>
                <div class="text-center">
                    <span class="inline-block bg-primary/10 text-primary text-xs sm:text-sm font-semibold px-3 sm:px-4 py-1.5 rounded-full mb-4">DÉBUTANT</span>
                    <div class="mb-6">
                        <span class="text-4xl sm:text-4xl font-black text-gray-900 monthly-price" data-monthly="4900" data-yearly="3900">4 900</span>
                        <span class="text-gray-500 text-sm sm:text-base"> FCFA/mois</span>
                    </div>
                    <p class="text-gray-600 mb-6 text-sm">Pour les petites boutiques</p>
                    <a href="{{ route('register') }}?plan=starter" class="block w-full bg-primary text-white font-semibold py-3 rounded-xl hover:bg-emerald-600 transition shadow-lg shadow-primary/25 text-sm sm:text-base">
                        Commencer maintenant
                    </a>
                </div>
                <ul class="mt-6 lg:mt-8 space-y-2 lg:space-y-3">
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>1 Boutique en ligne</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Jusqu'à <strong>50 produits</strong></span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Lien partageable</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Boutique personnalisée</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Commandes WhatsApp</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Paiements Wave/OM</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Statistiques de base</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Support par Whatsapp</span>
                    </li>

                </ul>
            </div>

            <!-- Plan Business -->
            <div class="pricing-card bg-white rounded-3xl border-2 border-gray-300 p-6 lg:p-8 transition-all duration-300" data-aos="fade-up" data-aos-delay="300">
                <div class="flex items-center justify-center w-12 h-12 bg-teal-100 rounded-xl mb-4 mx-auto">
                    <i class="fas fa-city text-teal-600 text-xl"></i>
                </div>
                <div class="text-center">
                    <span class="inline-block bg-secondary/10 text-secondary text-xs sm:text-sm font-semibold px-3 sm:px-4 py-1.5 rounded-full mb-4">PROFESSIONEL</span>
                    <div class="mb-6">
                        <span class="text-4xl sm:text-4xl font-black text-gray-900 monthly-price" data-monthly="9900" data-yearly="7900">9 900</span>
                        <span class="text-gray-500 text-sm sm:text-base"> FCFA/mois</span>
                    </div>
                    <p class="text-gray-600 mb-6 text-sm">Pour les commerces établis</p>
                    <a href="{{ route('register') }}?plan=business" class="block w-full bg-secondary text-white font-semibold py-3 rounded-xl hover:bg-gray-800 transition text-sm sm:text-base">
                        Débuter maintenant
                    </a>
                </div>
                <ul class="mt-6 lg:mt-8 space-y-2 lg:space-y-3">

                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span><strong>2 </strong> boutiques en ligne</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span> Jusqu'à  <strong>100</strong> produits</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Tout du plan Débutant</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Paiements Wave/OM</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Statistiques avancées</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Gestion des avis clients</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span> QR Code boutique</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span> Support WhatsApp H24</span>
                    </li>

                </ul>
            </div>

            <!-- Plan Enterprise -->
            <div class="pricing-card bg-white rounded-3xl border-2 border-gray-300 p-6 lg:p-8 transition-all duration-300" data-aos="fade-up" data-aos-delay="400">
                <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-xl mb-4 mx-auto">
                    <i class="fas fa-globe text-blue-600 text-xl"></i>
                </div>
                <div class="text-center">
                    <span class="inline-block bg-purple-100 text-purple-700 text-xs sm:text-sm font-semibold px-3 sm:px-4 py-1.5 rounded-full mb-4">BUSINESS</span>
                    <div class="mb-6">
                        <span class="text-4xl sm:text-4xl font-black text-gray-900 monthly-price" data-monthly="19900" data-yearly="15900">19 900</span>
                        <span class="text-gray-500 text-sm sm:text-base"> FCFA/mois</span>
                    </div>
                    <p class="text-gray-600 mb-6 text-sm">Pour les grandes structures</p>
                    <a href="{{ route('register') }}?plan=enterprise" class="block w-full bg-purple-600 text-white font-semibold py-3 rounded-xl hover:bg-purple-700 transition text-sm sm:text-base">
                        S'inscrire maintenant
                    </a>
                </div>
                <ul class="mt-6 lg:mt-8 space-y-2 lg:space-y-3">
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Boutiques <strong> illimitées</strong></span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Produits  <strong> illimités</strong></span>
                    </li>

                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Tout du plan Pro</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Formation et onboarding</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Rapports exportables</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Génération de Factures </span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Support prioritaire 24/7</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="fas fa-check text-primary mt-0.5 flex-shrink-0"></i>
                        <span>Gestion des stocks</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ==================== STATISTIQUES SIMPLES ==================== -->
<section id="statistiques" class="pt-4 lg:pt-0 pb-20 lg:pb-32 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            <!-- Texte -->
            <div data-aos="fade-right" data-aos-duration="800">
                <span class="text-primary font-semibold text-sm uppercase tracking-wider bg-primary/10 px-4 py-1.5 rounded-full">
                    Tableau de bord
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 mt-4 mb-4">
                    Retrouvez vos ventes en
                    <span class="gradient-text">un coup d'œil</span>
                </h2>
                <p class="text-base sm:text-lg text-gray-600 mb-6 leading-relaxed">
                    Pas de graphiques compliqués. Des chiffres clairs, des tendances lisibles et les produits qui cartonnent.
                    Vous savez exactement où va votre business.
                </p>

                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-check text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900">Ventes du jour</span>
                            <p class="text-sm text-gray-600">Suivez vos commandes en temps réel, par heure ou par jour.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-chart-simple text-blue-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900">Produits populaires</span>
                            <p class="text-sm text-gray-600">Identifiez vos meilleures ventes et réapprovisionnez au bon moment.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-arrow-trend-up text-purple-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900">Évolution mensuelle</span>
                            <p class="text-sm text-gray-600">Visualisez votre croissance et préparez vos prochaines actions.</p>
                        </div>
                    </li>
                </ul>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="bg-primary text-white font-semibold px-6 py-3 rounded-full hover:bg-emerald-600 transition shadow-lg shadow-primary/25">
                        <span class="hidden sm:inline">Découvrir le tableau de bord</span>
                        <span class="sm:hidden">Dashboard</span>
                    </a>
                    <a href="#demo" class="border-2 border-gray-300 text-gray-700 font-semibold px-6 py-3 rounded-full hover:border-primary hover:text-primary transition">
                        <i class="fas fa-play mr-1"></i> Voir une démo
                    </a>
                </div>
            </div>

            <!-- Mockup Dashboard -->
            <div data-aos="fade-left" data-aos-duration="800">
                <div class="bg-white rounded-2xl shadow-2xl p-4 sm:p-6 lg:p-8 border border-gray-200 relative overflow-hidden">

                    <!-- Badge "En direct" -->
                    <div class="absolute top-4 right-4 bg-green-500 text-white text-[10px] font-bold px-3 py-1 rounded-full flex items-center gap-1.5 animate-pulse">
                        <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                        Live
                    </div>

                    <!-- Header du dashboard -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-store text-primary"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Boutique</p>
                                <p class="text-sm font-bold text-gray-900">Ma Boutique Sénégal</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 text-xs text-gray-500">
                            <span class="flex items-center gap-1"><i class="far fa-calendar"></i> Déc 2024</span>
                            <span class="flex items-center gap-1"><i class="fas fa-sync"></i> 14h30</span>
                        </div>
                    </div>

                    <!-- KPIs -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                        <div class="bg-gray-50 rounded-xl p-3 text-center">
                            <p class="text-[10px] text-gray-400 uppercase font-semibold">Ventes</p>
                            <p class="text-xl font-black text-gray-900">24</p>
                            <p class="text-[10px] text-green-600">+12%</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 text-center">
                            <p class="text-[10px] text-gray-400 uppercase font-semibold">Chiffre</p>
                            <p class="text-xl font-black text-gray-900">52k</p>
                            <p class="text-[10px] text-gray-500">FCFA</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 text-center">
                            <p class="text-[10px] text-gray-400 uppercase font-semibold">Commandes</p>
                            <p class="text-xl font-black text-gray-900">8</p>
                            <p class="text-[10px] text-green-600">+4</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 text-center">
                            <p class="text-[10px] text-gray-400 uppercase font-semibold">Panier</p>
                            <p class="text-xl font-black text-gray-900">6 500</p>
                            <p class="text-[10px] text-gray-500">FCFA</p>
                        </div>
                    </div>

                    <!-- Graphique simplifié -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-semibold text-gray-700">Ventes des 7 derniers jours</p>
                            <span class="text-[10px] text-gray-400">vs semaine précédente</span>
                        </div>
                        <div class="flex items-end gap-2 h-20 sm:h-24 bg-gray-50 rounded-xl p-3">
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <div class="w-full bg-primary/20 rounded-t-sm h-8 relative">
                                    <div class="absolute bottom-0 left-0 right-0 bg-primary rounded-t-sm h-5"></div>
                                </div>
                                <span class="text-[9px] text-gray-400">Lun</span>
                            </div>
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <div class="w-full bg-primary/20 rounded-t-sm h-12 relative">
                                    <div class="absolute bottom-0 left-0 right-0 bg-primary rounded-t-sm h-9"></div>
                                </div>
                                <span class="text-[9px] text-gray-400">Mar</span>
                            </div>
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <div class="w-full bg-primary/20 rounded-t-sm h-16 relative">
                                    <div class="absolute bottom-0 left-0 right-0 bg-primary rounded-t-sm h-13"></div>
                                </div>
                                <span class="text-[9px] text-gray-400">Mer</span>
                            </div>
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <div class="w-full bg-primary/20 rounded-t-sm h-20 relative">
                                    <div class="absolute bottom-0 left-0 right-0 bg-primary rounded-t-sm h-17"></div>
                                </div>
                                <span class="text-[9px] text-gray-400">Jeu</span>
                            </div>
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <div class="w-full bg-primary/20 rounded-t-sm h-10 relative">
                                    <div class="absolute bottom-0 left-0 right-0 bg-primary rounded-t-sm h-7"></div>
                                </div>
                                <span class="text-[9px] text-gray-400">Ven</span>
                            </div>
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <div class="w-full bg-primary/20 rounded-t-sm h-14 relative">
                                    <div class="absolute bottom-0 left-0 right-0 bg-primary rounded-t-sm h-11"></div>
                                </div>
                                <span class="text-[9px] text-gray-400">Sam</span>
                            </div>
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <div class="w-full bg-primary/20 rounded-t-sm h-18 relative">
                                    <div class="absolute bottom-0 left-0 right-0 bg-primary rounded-t-sm h-15"></div>
                                </div>
                                <span class="text-[9px] text-gray-400">Dim</span>
                            </div>
                        </div>
                    </div>

                    <!-- Produits populaires -->
                    <div>
                        <p class="text-xs font-semibold text-gray-700 mb-2">Produits les plus vendus</p>
                        <div class="space-y-2">
                            <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-2">
                                <span class="text-[10px] font-bold text-gray-400 w-4">1</span>
                                <div class="w-8 h-8 bg-primary/10 rounded flex items-center justify-center text-primary text-xs">👕</div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-gray-800">T-shirt Sénégal</p>
                                    <p class="text-[10px] text-gray-500">12 vendus</p>
                                </div>
                                <span class="text-xs font-bold text-gray-900">3 500 F</span>
                            </div>
                            <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-2">
                                <span class="text-[10px] font-bold text-gray-400 w-4">2</span>
                                <div class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center text-blue-600 text-xs">📱</div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-gray-800">Coque iPhone</p>
                                    <p class="text-[10px] text-gray-500">8 vendus</p>
                                </div>
                                <span class="text-xs font-bold text-gray-900">2 000 F</span>
                            </div>
                            <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-2">
                                <span class="text-[10px] font-bold text-gray-400 w-4">3</span>
                                <div class="w-8 h-8 bg-yellow-100 rounded flex items-center justify-center text-yellow-600 text-xs">🍯</div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-gray-800">Miel naturel</p>
                                    <p class="text-[10px] text-gray-500">5 vendus</p>
                                </div>
                                <span class="text-xs font-bold text-gray-900">4 500 F</span>
                            </div>
                        </div>
                    </div>

                    <!-- Lien vers dashboard -->
                    <div class="mt-4 text-right">
                        <a href="{{ route('register') }}" class="text-xs text-primary font-semibold hover:underline flex items-center justify-end gap-1">
                            Voir le tableau complet <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>


<!-- ==================== COMMANDES WHATSAPP ==================== -->
{{-- <section id="commandes-whatsapp" class="py-20 lg:py-32 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            <!-- Mockup WhatsApp (à gauche) -->
            <div data-aos="fade-right" data-aos-duration="800" class="order-2 lg:order-1">
                <div class="relative max-w-md mx-auto">

                    <!-- Téléphone frame -->
                    <div class="bg-gray-900 rounded-[3rem] p-3 shadow-2xl">
                        <div class="bg-white rounded-[2.5rem] overflow-hidden">

                            <!-- Notch -->
                            <div class="bg-gray-900 h-6 flex items-center justify-center rounded-t-[2.5rem]">
                                <div class="w-16 h-4 bg-gray-900 rounded-b-2xl"></div>
                            </div>

                            <!-- Écran WhatsApp -->
                            <div class="bg-[#E5DDD5] p-3 min-h-[450px] sm:min-h-[500px]">

                                <!-- Header WhatsApp -->
                                <div class="bg-[#075E54] text-white rounded-t-xl p-3 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold">S</div>
                                    <div>
                                        <p class="text-sm font-semibold">FatiHad Boutique</p>
                                        <p class="text-[10px] text-white/70">En ligne</p>
                                    </div>
                                    <div class="ml-auto flex gap-3 text-sm">
                                        <i class="fas fa-phone"></i>
                                        <i class="fas fa-video"></i>
                                    </div>
                                </div>

                                <!-- Conversation -->
                                <div class="p-3 space-y-3">

                                    <!-- Message client -->
                                    <div class="flex justify-start">
                                        <div class="bg-white rounded-lg p-3 max-w-[80%] shadow-sm rounded-bl-none">
                                            <p class="text-sm text-gray-800 font-medium">Bonjour ! 👋</p>
                                            <p class="text-sm text-gray-800 mt-1">Je voudrais commander le T-shirt Sénégal taille M.</p>
                                            <span class="text-[10px] text-gray-400 mt-1 block text-right">14:32</span>
                                        </div>
                                    </div>

                                    <!-- Message automatique boutique -->
                                    <div class="flex justify-end">
                                        <div class="bg-[#DCF8C6] rounded-lg p-3 max-w-[80%] shadow-sm rounded-br-none">
                                            <p class="text-sm text-gray-800">Bonjour ! ✅</p>
                                            <p class="text-sm text-gray-800 mt-1">Le T-shirt Sénégal taille M est disponible à <strong>3 500 FCFA</strong>.</p>
                                            <p class="text-sm text-gray-800 mt-1">Voulez-vous passer commande ?</p>
                                            <span class="text-[10px] text-gray-400 mt-1 block text-right">14:33</span>
                                            <span class="text-[10px] text-blue-500 block text-right"><i class="fas fa-check-double"></i> Lu</span>
                                        </div>
                                    </div>

                                    <!-- Message client -->
                                    <div class="flex justify-start">
                                        <div class="bg-white rounded-lg p-3 max-w-[80%] shadow-sm rounded-bl-none">
                                            <p class="text-sm text-gray-800">Oui, je prends !</p>
                                            <p class="text-sm text-gray-800 mt-1">Comment je paie ?</p>
                                            <span class="text-[10px] text-gray-400 mt-1 block text-right">14:35</span>
                                        </div>
                                    </div>

                                    <!-- Message automatique avec lien de paiement -->
                                    <div class="flex justify-end">
                                        <div class="bg-[#DCF8C6] rounded-lg p-3 max-w-[80%] shadow-sm rounded-br-none">
                                            <p class="text-sm text-gray-800">Parfait ! 🎉</p>
                                            <p class="text-sm text-gray-800 mt-1">Vous pouvez payer via <strong>Wave</strong> ou <strong>Orange Money</strong>.</p>
                                            <div class="mt-2 bg-green-100 border border-green-200 rounded-lg p-2 text-center">
                                                <p class="text-[10px] text-gray-500">Lien de paiement</p>
                                                <p class="text-xs font-bold text-primary break-all">wave.me/pay/3d5FgH7</p>
                                            </div>
                                            <p class="text-sm text-gray-800 mt-2">Cliquez sur le lien pour payer. Livraison sous 24h.</p>
                                            <span class="text-[10px] text-gray-400 mt-1 block text-right">14:36</span>
                                            <span class="text-[10px] text-blue-500 block text-right"><i class="fas fa-check-double"></i> Lu</span>
                                        </div>
                                    </div>

                                    <!-- Saisie message -->
                                    <div class="flex items-center gap-2 bg-white rounded-full p-2 mt-2">
                                        <i class="far fa-smile text-gray-500"></i>
                                        <input type="text" placeholder="Écrivez un message..." class="flex-1 bg-transparent text-sm outline-none text-gray-700 placeholder-gray-400">
                                        <i class="fas fa-paper-plane text-[#075E54]"></i>
                                    </div>

                                </div>
                            </div>

                            <!-- Home bar -->
                            <div class="flex justify-center pb-2 bg-white">
                                <div class="w-20 h-1 bg-gray-300 rounded-full"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Badge flottant -->
                    <div class="absolute -top-4 -right-4 bg-green-500 text-white rounded-full px-4 py-2 shadow-xl flex items-center gap-2 animate-bounce">
                        <i class="fab fa-whatsapp text-xl"></i>
                        <span class="text-xs font-bold">Direct</span>
                    </div>

                </div>
            </div>

            <!-- Texte (à droite) -->
            <div data-aos="fade-left" data-aos-duration="800" class="order-1 lg:order-2">
                <span class="text-primary font-semibold text-sm uppercase tracking-wider bg-primary/10 px-4 py-1.5 rounded-full">
                    Intégration WhatsApp
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 mt-4 mb-4">
                    Vos clients commandent
                    <span class="gradient-text">en un clic</span>
                </h2>
                <p class="text-base sm:text-lg text-gray-600 mb-6 leading-relaxed">
                    Vos clients parcourent votre catalogue, cliquent sur <strong>"Commander via WhatsApp"</strong> et le message est automatiquement pré-rempli avec les détails du produit.
                    Vous recevez la commande instantanément. <strong>Sans application, sans complication.</strong>
                </p>

                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-bolt text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900">Commande instantanée</span>
                            <p class="text-sm text-gray-600">Dès que le client clique, le message WhatsApp s'ouvre avec le produit et le prix déjà remplis.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-check-circle text-blue-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900">Aucune installation</span>
                            <p class="text-sm text-gray-600">Vos clients n'ont rien à installer. WhatsApp est déjà sur leur téléphone. Ils commandent en 2 secondes.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-store text-purple-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900">Tout est centralisé</span>
                            <p class="text-sm text-gray-600">Toutes les commandes WhatsApp remontent dans votre tableau de bord. Vous ne perdez aucune vente.</p>
                        </div>
                    </li>
                </ul>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="bg-primary text-white font-semibold px-6 py-3 rounded-full hover:bg-emerald-600 transition shadow-lg shadow-primary/25">
                        <i class="fab fa-whatsapp mr-2"></i>
                        <span class="hidden sm:inline">Activer WhatsApp</span>
                        <span class="sm:hidden">WhatsApp</span>
                    </a>
                    <a href="#demo" class="border-2 border-gray-300 text-gray-700 font-semibold px-6 py-3 rounded-full hover:border-primary hover:text-primary transition">
                        <i class="fas fa-play mr-1"></i> Voir la démo
                    </a>
                </div>

                <div class="mt-6 flex items-center gap-2 text-sm text-gray-500">
                    <i class="fas fa-shield-alt text-primary"></i>
                    <span>Vos conversations sont sécurisées et respectent la confidentialité.</span>
                </div>
            </div>

        </div>
    </div>
</section>--}}


<!-- ==================== PAIEMENTS MOBILES ==================== -->
<section id="paiements-mobiles" class="pt-4 lg:pt-0 pb-20 lg:pb-32 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            <!-- Texte (à gauche) -->
            <div data-aos="fade-right" data-aos-duration="800" class="order-2 lg:order-1">
                <span class="text-primary font-semibold text-sm uppercase tracking-wider bg-primary/10 px-4 py-1.5 rounded-full">
                    Paiements simplifiés
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 mt-4 mb-4">
                    Encaissez avec
                    <span class="gradient-text">Wave & Orange Money</span>
                </h2>
                <p class="text-base sm:text-lg text-gray-600 mb-6 leading-relaxed">
                    Vos clients paient directement depuis leur téléphone. <strong>Pas de terminal de paiement, pas de frais cachés.</strong>
                    Les fonds arrivent instantanément sur votre compte Wave ou Orange Money. Simple, rapide, sécurisé.
                </p>

                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-bolt text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900">Paiement en 1 clic</span>
                            <p class="text-sm text-gray-600">Le client sélectionne Wave ou Orange Money, confirme avec son code PIN. C'est tout. L'argent arrive immédiatement.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-shield-alt text-blue-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900">Sécurisé et fiable</span>
                            <p class="text-sm text-gray-600">Les paiements sont cryptés et traités directement par Wave et Orange Money. Vous recevez une confirmation instantanée.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-mobile-screen text-purple-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900">Accessible à tous</span>
                            <p class="text-sm text-gray-600">Plus de 90 % des Sénégalais ont Wave ou Orange Money. Vous touchez tous vos clients, pas seulement ceux avec une carte bancaire.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="bg-primary text-white font-semibold px-6 py-3 rounded-full hover:bg-emerald-600 transition shadow-lg shadow-primary/25">
                        <i class="fas fa-money-bill-wave mr-2"></i>
                        <span class="hidden sm:inline">Activer les paiements</span>
                        <span class="sm:hidden">Paiements</span>
                    </a>
                    <a href="#demo" class="border-2 border-gray-300 text-gray-700 font-semibold px-6 py-3 rounded-full hover:border-primary hover:text-primary transition">
                        <i class="fas fa-play mr-1"></i> Voir la démo</a>
                </div>

                <div class="mt-6 flex items-center gap-2 text-sm text-gray-500 bg-white/80 rounded-lg px-4 py-3 border border-gray-200">
                    <i class="fas fa-check-circle text-primary"></i>
                    <span><strong>0 % de commission</strong> sur vos ventes. Vous gardez 100 % de votre chiffre d'affaires.</span>
                </div>
            </div>

            <!-- Mockup Paiements (à droite) -->
            <div data-aos="fade-left" data-aos-duration="800" class="order-1 lg:order-2">
                <div class="relative max-w-md mx-auto">

                    <!-- Téléphone frame -->
                    <div class="bg-gray-900 rounded-[3rem] p-3 shadow-2xl">
                        <div class=" rounded-[2.5rem] overflow-hidden">

                            <!-- Notch -->
                            <div class="bg-gray-900 h-6 flex items-center justify-center rounded-t-[2.5rem]">
                                <div class="w-16 h-4 bg-gray-900 rounded-b-2xl"></div>
                            </div>

                            <!-- Écran - Page de paiement -->
                            <div class="bg-white p-4 min-h-[450px] sm:min-h-[500px]">

                                <!-- Header boutique -->
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center">
                                        <i class="fas fa-store text-white"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">FatiHad Boutique</p>
                                        <p class="text-xs text-gray-500">Commande #2024-12-001</p>
                                    </div>
                                </div>

                                <!-- Résumé commande -->
                                <div class="bg-gray-50 rounded-xl p-4 mb-4 border border-gray-200">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">T-shirt Sénégal (M)</span>
                                        <span class="font-medium text-gray-900">3 500 FCFA</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm mt-2">
                                        <span class="text-gray-600">Frais de livraison</span>
                                        <span class="font-medium text-gray-900">1 000 FCFA</span>
                                    </div>
                                    <div class="border-t border-gray-200 mt-2 pt-2 flex items-center justify-between font-bold">
                                        <span class="text-gray-800">Total</span>
                                        <span class="text-primary text-lg">4 500 FCFA</span>
                                    </div>
                                </div>

                                <!-- Options de paiement -->
                                <p class="text-xs font-semibold text-gray-700 mb-3">Choisissez votre moyen de paiement</p>

                                <div class="space-y-3">
                                    <!-- Wave -->
                                    <div class="border-2 border-gray-200 rounded-xl p-3 cursor-pointer hover:border-primary transition-all duration-300 group">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-[#00B0F0]/10 rounded-lg flex items-center justify-center p-1.5">
                                                <img src="{{ asset('images/wave.png') }}" alt="Wave" class="w-full h-full object-contain">
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-900 text-sm">Wave</p>
                                                <p class="text-xs text-gray-500">Paiement mobile sécurisé</p>
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 group-hover:border-primary flex items-center justify-center">
                                                <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 group-hover:scale-100 transition-all duration-300"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Orange Money -->
                                    <div class="border-2 border-gray-200 rounded-xl p-3 cursor-pointer hover:border-primary transition-all duration-300 group">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-[#FF7900]/10 rounded-lg flex items-center justify-center p-1.5">
                                                <img src="{{ asset('images/orange-money.png') }}" alt="Orange Money" class="w-full h-full object-contain">
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-900 text-sm">Orange Money</p>
                                                <p class="text-xs text-gray-500">Paiement mobile sécurisé</p>
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 group-hover:border-primary flex items-center justify-center">
                                                <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 group-hover:scale-100 transition-all duration-300"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bouton payer -->
                                <div class="mt-6">
                                    <button class="w-full bg-primary text-white font-semibold py-3 rounded-xl hover:bg-emerald-600 transition shadow-lg shadow-primary/25 flex items-center justify-center gap-2 text-sm">
                                        <i class="fas fa-lock"></i>
                                        Payer 4 500 FCFA
                                    </button>
                                    <p class="text-[10px] text-gray-400 text-center mt-2">
                                        <i class="fas fa-shield-alt mr-1"></i>
                                        Paiement sécurisé par Wave / Orange Money
                                    </p>
                                </div>

                                <!-- Logos -->
                                <div class="flex justify-center gap-4 mt-4">
                                    <div class="text-xs text-gray-400 flex items-center gap-1">
                                        <i class="fab fa-cc-visa"></i> Visa
                                    </div>
                                    <div class="text-xs text-gray-400 flex items-center gap-1">
                                        <i class="fab fa-cc-mastercard"></i> Mastercard
                                    </div>
                                    <div class="text-xs text-gray-400 flex items-center gap-1">
                                        <i class="fas fa-mobile-alt"></i> Mobile
                                    </div>
                                </div>
                            </div>

                            <!-- Home bar -->
                            <div class="flex justify-center pb-2 bg-white">
                                <div class="w-20 h-1 bg-gray-300 rounded-full"></div>
                            </div>
                        </div>
                    </div>



                </div>
            </div>

        </div>
    </div>
</section>

<!-- ==================== LIEN PERSONNALISÉ ==================== -->
<section id="lien-personnalise" class="pt-4 lg:pt-4 pb-20 lg:pb-32 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            <!-- Mockup Lien personnalisé (à gauche) -->
            <div data-aos="fade-right" data-aos-duration="800" class="order-2 lg:order-1">
                <div class="relative max-w-md mx-auto">

                    <!-- Carte style réseau social -->
                    <div class="bg-white rounded-3xl shadow-2xl border border-gray-200 overflow-hidden">

                        <!-- Header style Facebook/Instagram -->
                        <div class="bg-gradient-to-r from-primary to-emerald-600 p-4 text-white">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-store text-white"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold">FatiHad Boutique</p>
                                        <p class="text-[10px] text-white/80">En ligne • 24/7</p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <i class="fas fa-ellipsis-h text-white/80"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Image produit -->
                        <div class="bg-gray-100 h-40 flex items-center justify-center relative">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                            <div class="text-center">
                                <div class="w-16 h-16 bg-white/80 rounded-full mx-auto flex items-center justify-center shadow-lg">
                                    <i class="fas fa-camera text-2xl text-primary"></i>
                                </div>
                                <p class="text-xs text-gray-500 mt-2 font-medium">T-shirt Sénégal - 3 500 FCFA</p>
                            </div>
                            <!-- Bouton "Acheter" flottant -->
                            <div class="absolute bottom-4 right-4 bg-primary text-white text-xs font-bold px-4 py-1.5 rounded-full shadow-lg">
                                Acheter
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-full">En stock</span>
                                <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full">⭐ 4.8</span>
                            </div>
                            <h3 class="font-bold text-gray-900 text-lg">T-shirt Sénégal 🇸🇳</h3>
                            <p class="text-sm text-gray-600 mt-1">Édition limitée - 100% coton bio - Taille M</p>

                            <!-- Prix -->
                            <div class="flex items-center gap-2 mt-3">
                                <span class="text-2xl font-black text-primary">3 500</span>
                                <span class="text-sm text-gray-500">FCFA</span>
                                <span class="text-sm text-gray-400 line-through ml-2">5 000 FCFA</span>
                            </div>

                            <!-- Bouton commander -->
                            <div class="mt-4 flex gap-2">
                                <button class="flex-1 bg-primary text-white font-semibold py-2.5 rounded-xl hover:bg-emerald-600 transition shadow-lg shadow-primary/25 text-sm flex items-center justify-center gap-2">
                                    <i class="fab fa-whatsapp"></i>
                                    Commander
                                </button>
                                <button class="w-10 h-10 border-2 border-gray-200 rounded-xl flex items-center justify-center hover:border-primary transition text-gray-400 hover:text-primary">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Footer avec lien -->
                        <div class="bg-gray-50 border-t border-gray-200 p-3 flex items-center justify-between">
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <i class="fas fa-link text-primary"></i>
                                <span>FatiHad.com/boutique</span>
                            </div>
                            <div class="flex gap-2">
                                <i class="fab fa-facebook text-gray-400 hover:text-blue-600 cursor-pointer transition"></i>
                                <i class="fab fa-instagram text-gray-400 hover:text-pink-600 cursor-pointer transition"></i>
                                <i class="fab fa-whatsapp text-gray-400 hover:text-green-600 cursor-pointer transition"></i>
                            </div>
                        </div>

                    </div>





                </div>
            </div>

            <!-- Texte (à droite) -->
            <div data-aos="fade-left" data-aos-duration="800" class="order-1 lg:order-2">
                <span class="text-primary font-semibold text-sm uppercase tracking-wider bg-primary/10 px-4 py-1.5 rounded-full">
                    Votre adresse unique
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 mt-4 mb-4">
                    Trouvez votre boutique
                    <span class="gradient-text">à portée de clic</span>
                </h2>
                <p class="text-base sm:text-lg text-gray-600 mb-6 leading-relaxed">
                    Chaque boutique FatiHad a son propre lien unique : <strong>FatiHad.com/votre-boutique</strong>.
                    Partagez-le sur Facebook, Instagram, WhatsApp ou TikTok. <strong>Vos clients commandent 24h/24, 7j/7.</strong>
                </p>

                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-link text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900">Un lien, tout le monde</span>
                            <p class="text-sm text-gray-600">Pas de site complexe à construire. FatiHad génère votre lien automatiquement. Vous le partagez en 1 seconde.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-share-alt text-blue-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900">Partagez partout</span>
                            <p class="text-sm text-gray-600">En story Instagram, en statut WhatsApp, en post Facebook, en vidéo TikTok. Votre boutique est toujours accessible.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-moon text-purple-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900">Ouverte 24h/24</span>
                            <p class="text-sm text-gray-600">Même quand vous dormez, votre boutique travaille. Vos clients peuvent commander à 2h du matin, vous traitez la commande au réveil.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="bg-primary text-white font-semibold px-6 py-3 rounded-full hover:bg-emerald-600 transition shadow-lg shadow-primary/25">
                        <i class="fas fa-link mr-2"></i>
                        <span class="hidden sm:inline">Créer mon lien</span>
                        <span class="sm:hidden">Mon lien</span>
                    </a>
                    <a href="#demo" class="border-2 border-gray-300 text-gray-700 font-semibold px-6 py-3 rounded-full hover:border-primary hover:text-primary transition">
                        <i class="fas fa-play mr-1"></i> Voir la démo
                    </a>
                </div>

                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-start gap-3">
                    <i class="fas fa-lightbulb text-blue-600 mt-0.5"></i>
                    <div>
                        <p class="text-sm text-gray-700">
                            <strong>Astuce :</strong> Ajoutez votre lien dans votre bio Instagram et sur votre profil Facebook. C'est votre vitrine 24h/24.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<!-- ==================== CATALOGUE ILLIMITÉ ==================== -->
<section id="catalogue-illimite" class="pt-4 lg:pt-4 pb-20 lg:pb-32 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            <!-- Texte (à gauche) -->
            <div data-aos="fade-right" data-aos-duration="800" class="order-2 lg:order-1">
                <span class="text-primary font-semibold text-sm uppercase tracking-wider bg-primary/10 px-4 py-1.5 rounded-full">
                    Gestion de produits
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 mt-4 mb-4">
                    Gérer un catalogue
                    <span class="gradient-text">sans limites</span>
                </h2>
                <p class="text-base sm:text-lg text-gray-600 mb-6 leading-relaxed">
                    Ajoutez tous vos produits avec photos, prix et descriptions. Organisez-les en catégories pour une navigation fluide.
                    <strong>Gérez des centaines de produits</strong> aussi facilement que s'il y en avait 10.
                </p>

                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-plus-circle text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900">Ajoutez en quelques secondes</span>
                            <p class="text-sm text-gray-600">Téléchargez vos photos, saisissez le prix et la description. Un formulaire simple pour un résultat professionnel.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-folder-open text-blue-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900">Organisez en catégories</span>
                            <p class="text-sm text-gray-600">Créez des catégories (Vêtements, Électronique, Cosmétiques...) pour aider vos clients à trouver ce qu'ils cherchent en 2 clics.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-pen-to-square text-purple-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900">Modifiez à tout moment</span>
                            <p class="text-sm text-gray-600">Prix, stock, description, photos. Modifiez tout en temps réel. Votre boutique est toujours à jour.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="bg-primary text-white font-semibold px-6 py-3 rounded-full hover:bg-emerald-600 transition shadow-lg shadow-primary/25">
                        <i class="fas fa-boxes mr-2"></i>
                        <span class="hidden sm:inline">Ajouter mes produits</span>
                        <span class="sm:hidden">Produits</span>
                    </a>
                    <a href="#demo" class="border-2 border-gray-300 text-gray-700 font-semibold px-6 py-3 rounded-full hover:border-primary hover:text-primary transition">
                        <i class="fas fa-play mr-1"></i> Voir la démo
                    </a>
                </div>

                <div class="mt-6 bg-white rounded-xl p-3 border border-gray-200 flex items-start gap-3">
                    <i class="fas fa-rocket text-primary mt-0.5"></i>
                    <div>
                        <p class="text-sm text-gray-700">
                            <strong>Plan Gratuit :</strong> jusqu'à 10 produits. <strong>Plan Business :</strong> produits illimités. Passez à l'échelle quand vous voulez.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Mockup Catalogue (à droite) - RÉDUIT SUR MOBILE SEULEMENT -->
            <div data-aos="fade-left" data-aos-duration="800" class="order-1 lg:order-2">
                <!-- Wrapper qui réduit la largeur sur mobile et garde le desktop identique -->
                <div class="relative max-w-[350px] sm:max-w-sm lg:max-w-md mx-auto lg:mx-0 lg:ml-auto">

                    <!-- Interface tableau de bord -->
                    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">

                        <!-- Header -->
                        <div class="bg-gray-50 border-b border-gray-200 p-4 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-store text-primary"></i>
                                <span class="font-bold text-gray-900 text-sm">Mon Catalogue</span>
                            </div>
                            <button class="bg-primary text-white text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-emerald-600 transition">
                                <i class="fas fa-plus mr-1"></i> Ajouter
                            </button>
                        </div>

                        <!-- Barre de recherche & filtres -->
                        <div class="p-3 border-b border-gray-200">
                            <div class="flex gap-2">
                                <div class="flex-1 relative">
                                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                    <input type="text" placeholder="Rechercher un produit..." class="w-full pl-8 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm outline-none focus:border-primary transition">
                                </div>
                                <button class="px-3 py-2 bg-gray-100 rounded-lg text-gray-600 hover:bg-gray-200 transition">
                                    <i class="fas fa-sliders-h text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Liste des catégories -->
                        <div class="px-3 py-2 border-b border-gray-200 flex gap-2 overflow-x-auto no-scrollbar">
                            <span class="bg-primary text-white text-[10px] font-semibold px-3 py-1 rounded-full whitespace-nowrap">Tous</span>
                            <span class="bg-gray-100 text-gray-700 text-[10px] font-semibold px-3 py-1 rounded-full whitespace-nowrap hover:bg-gray-200 cursor-pointer transition">Vêtements</span>
                            <span class="bg-gray-100 text-gray-700 text-[10px] font-semibold px-3 py-1 rounded-full whitespace-nowrap hover:bg-gray-200 cursor-pointer transition">Électronique</span>
                            <span class="bg-gray-100 text-gray-700 text-[10px] font-semibold px-3 py-1 rounded-full whitespace-nowrap hover:bg-gray-200 cursor-pointer transition">Cosmétiques</span>
                            <span class="bg-gray-100 text-gray-700 text-[10px] font-semibold px-3 py-1 rounded-full whitespace-nowrap hover:bg-gray-200 cursor-pointer transition">Maison</span>
                        </div>

                        <!-- Grille produits -->
                        <div class="p-3 grid grid-cols-2 gap-3">

                            <!-- Produit 1 -->
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-all duration-300 group">
                                <div class="h-24 bg-gradient-to-br from-primary/20 to-emerald-200 relative">
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <i class="fas fa-tshirt text-3xl text-primary/60"></i>
                                    </div>
                                    <div class="absolute top-2 right-2 bg-white/80 rounded-full px-2 py-0.5 text-[9px] font-bold text-gray-600">Stock: 12</div>
                                </div>
                                <div class="p-2">
                                    <p class="text-xs font-semibold text-gray-900 truncate">T-shirt Sénégal</p>
                                    <p class="text-[10px] text-gray-500">Vêtements</p>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-sm font-bold text-primary">3 500 F</span>
                                        <div class="opacity-0 group-hover:opacity-100 transition">
                                            <i class="fas fa-edit text-gray-400 hover:text-primary cursor-pointer"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Produit 2 -->
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-all duration-300 group">
                                <div class="h-24 bg-gradient-to-br from-blue-100 to-blue-200 relative">
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <i class="fas fa-mobile-alt text-3xl text-blue-400/60"></i>
                                    </div>
                                    <div class="absolute top-2 right-2 bg-white/80 rounded-full px-2 py-0.5 text-[9px] font-bold text-gray-600">Stock: 8</div>
                                </div>
                                <div class="p-2">
                                    <p class="text-xs font-semibold text-gray-900 truncate">Coque iPhone 15</p>
                                    <p class="text-[10px] text-gray-500">Électronique</p>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-sm font-bold text-primary">2 000 F</span>
                                        <div class="opacity-0 group-hover:opacity-100 transition">
                                            <i class="fas fa-edit text-gray-400 hover:text-primary cursor-pointer"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Produit 3 -->
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-all duration-300 group">
                                <div class="h-24 bg-gradient-to-br from-yellow-100 to-yellow-200 relative">
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <i class="fas fa-jar text-3xl text-yellow-500/60"></i>
                                    </div>
                                    <div class="absolute top-2 right-2 bg-white/80 rounded-full px-2 py-0.5 text-[9px] font-bold text-gray-600">Stock: 5</div>
                                </div>
                                <div class="p-2">
                                    <p class="text-xs font-semibold text-gray-900 truncate">Miel Naturel</p>
                                    <p class="text-[10px] text-gray-500">Cosmétiques</p>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-sm font-bold text-primary">4 500 F</span>
                                        <div class="opacity-0 group-hover:opacity-100 transition">
                                            <i class="fas fa-edit text-gray-400 hover:text-primary cursor-pointer"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Produit 4 -->
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-all duration-300 group">
                                <div class="h-24 bg-gradient-to-br from-purple-100 to-purple-200 relative">
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <i class="fas fa-couch text-3xl text-purple-400/60"></i>
                                    </div>
                                    <div class="absolute top-2 right-2 bg-white/80 rounded-full px-2 py-0.5 text-[9px] font-bold text-gray-600">Stock: 3</div>
                                </div>
                                <div class="p-2">
                                    <p class="text-xs font-semibold text-gray-900 truncate">Coussin décoratif</p>
                                    <p class="text-[10px] text-gray-500">Maison</p>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-sm font-bold text-primary">6 500 F</span>
                                        <div class="opacity-0 group-hover:opacity-100 transition">
                                            <i class="fas fa-edit text-gray-400 hover:text-primary cursor-pointer"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Pagination -->
                        <div class="px-3 py-2 border-t border-gray-200 flex items-center justify-between text-xs text-gray-500">
                            <span>4 produits affichés</span>
                            <div class="flex gap-1">
                                <button class="w-6 h-6 rounded bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition"><i class="fas fa-chevron-left text-[10px]"></i></button>
                                <button class="w-6 h-6 rounded bg-primary text-white flex items-center justify-center text-[10px] font-bold">1</button>
                                <button class="w-6 h-6 rounded bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition text-[10px]">2</button>
                                <button class="w-6 h-6 rounded bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition"><i class="fas fa-chevron-right text-[10px]"></i></button>
                            </div>
                        </div>

                    </div>



                </div>
            </div>

        </div>
    </div>
</section>

<!-- ==================== QR CODE ==================== -->
<section id="qr-code" class="pt-4 lg:pt-4 pb-20 lg:pb-32 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            <!-- Mockup Comptoir avec votre image (à gauche) - RÉDUIT SUR MOBILE -->
            <div data-aos="fade-right" data-aos-duration="800" class="order-2 lg:order-1">
                <!-- Wrapper qui réduit la largeur sur mobile et garde le desktop identique -->
                <div class="relative max-w-[320px] sm:max-w-sm lg:max-w-md mx-auto lg:mx-0">

                    <!-- Carte Comptoir / Flyer -->
                    <div class="bg-white rounded-3xl shadow-2xl border border-gray-200 overflow-hidden p-8 text-center relative">

                        <!-- Header -->
                        <div class="mb-4">
                            <div class="w-14 h-14 bg-primary rounded-xl flex items-center justify-center mx-auto">
                                <i class="fas fa-store text-white text-xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mt-3">FatiHad Boutique</h3>
                            <p class="text-xs text-gray-500">Scannez pour commander en ligne</p>
                        </div>

                        <!-- VOTRE IMAGE ICI (avec son cadre) -->
                        <div class="bg-gray-50 p-4 rounded-2xl border-2 border-dashed border-primary/30 mx-auto w-48 h-48 flex items-center justify-center shadow-sm relative">
                            <img
                                src="{{ asset('images/qr-code-badges.png') }}"
                                alt="QR Code FatiHad"
                                class="w-full h-full object-contain rounded-xl"
                            >
                        </div>

                        <!-- Lien en dessous -->
                        <div class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-500 bg-gray-50 rounded-full px-4 py-2 border border-gray-200">
                            <i class="fas fa-link text-primary"></i>
                            <span>FatiHad.com/boutique</span>
                            <button class="text-primary hover:text-emerald-600 transition ml-1" onclick="navigator.clipboard?.writeText('FatiHad.com/boutique')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>

                        <!-- Instructions visuelles -->
                        <div class="mt-4 flex justify-center gap-4 text-[10px] text-gray-400">
                            <span class="flex items-center gap-1"><i class="fas fa-camera"></i> Ouvrez l'appareil photo</span>
                            <span class="flex items-center gap-1"><i class="fas fa-qrcode"></i> Scannez</span>
                            <span class="flex items-center gap-1"><i class="fas fa-store"></i> Commandez</span>
                        </div>

                    </div>

                    <!-- Décoration flottante d'ambiance -->
                    <div class="absolute -top-8 -left-8 w-16 h-16 bg-primary/5 rounded-full blur-xl"></div>
                    <div class="absolute -bottom-8 -right-8 w-16 h-16 bg-blue-500/5 rounded-full blur-xl"></div>

                </div>
            </div>

            <!-- Texte (à droite) -->
            <div data-aos="fade-left" data-aos-duration="800" class="order-1 lg:order-2">
                <span class="text-primary font-semibold text-sm uppercase tracking-wider bg-primary/10 px-4 py-1.5 rounded-full">
                    Accès instantané
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 mt-4 mb-4">
                    Créez un QR code pour
                    <span class="gradient-text">votre boutique</span>
                </h2>
                <p class="text-base sm:text-lg text-gray-600 mb-6 leading-relaxed">
                    Générez un <strong>QR code unique</strong> en forme de badge prêt à l'emploi. Imprimez-le sur votre comptoir, vos flyers, vos cartes de visite ou vos emballages.
                    En un scan, vos clients accèdent à votre catalogue et commandent en quelques secondes.
                </p>

                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-qrcode text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900">Génération automatique</span>
                            <p class="text-sm text-gray-600">FatiHad crée votre badge QR code dès l'ouverture de votre boutique. Design moderne, prêt à imprimer.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-print text-blue-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900">Imprimez et collez partout</span>
                            <p class="text-sm text-gray-600">Flyers, stickers de comptoir, emballages. Le format badge est conçu pour être visible et inviter à scanner.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-mobile-screen-button text-purple-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900">Scan en 1 seconde</span>
                            <p class="text-sm text-gray-600">Le client scanne avec son smartphone, la boutique s'ouvre instantanément. Il commande et paie en 3 clics.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="bg-primary text-white font-semibold px-6 py-3 rounded-full hover:bg-emerald-600 transition shadow-lg shadow-primary/25">
                        <i class="fas fa-qrcode mr-2"></i>
                        <span class="hidden sm:inline">Générer mon QR code</span>
                        <span class="sm:hidden">QR code</span>
                    </a>
                    <a href="#demo" class="border-2 border-gray-300 text-gray-700 font-semibold px-6 py-3 rounded-full hover:border-primary hover:text-primary transition">
                        <i class="fas fa-play mr-1"></i> Voir la démo
                    </a>
                </div>

                <div class="mt-6 bg-white rounded-xl p-3 border border-gray-200 flex items-start gap-3">
                    <i class="fas fa-lightbulb text-yellow-500 mt-0.5"></i>
                    <div>
                        <p class="text-sm text-gray-700">
                            <strong>Idée :</strong> Imprimez votre badge QR code sur vos sacs et emballages. Chaque client devient un ambassadeur.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>



<!-- ==================== TESTIMONIALS ==================== -->
<section id="temoignages" class="pt-4 lg:pt-0 pb-20 lg:pb-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-primary font-semibold text-sm uppercase tracking-wider bg-primary/10 px-4 py-1.5 rounded-full">Ils nous font confiance</span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 mt-4 mb-4">
                Ce que disent nos
                <span class="gradient-text">commerçants</span>
            </h2>
        </div>

        <div class="grid md:grid-cols-3 gap-6 lg:gap-8">
            <div class="testimonial-card bg-white border border-gray-100 rounded-2xl p-6 lg:p-8" data-aos="fade-up" data-aos-delay="100">
                <div class="flex mb-4 text-yellow-400 text-lg">★★★★★</div>
                <p class="text-gray-700 mb-6 leading-relaxed text-sm sm:text-base">"J'ai créé ma boutique de vêtements en une soirée. Mes clientes adorent le catalogue WhatsApp. Mes ventes ont augmenté de 40% depuis."</p>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center font-bold text-pink-600 text-sm">AD</div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm sm:text-base">Aminata Diop</p>
                        <p class="text-xs sm:text-sm text-gray-500">Boutique Mode Dakar</p>
                    </div>
                </div>
            </div>

            <div class="testimonial-card bg-white border border-gray-100 rounded-2xl p-6 lg:p-8" data-aos="fade-up" data-aos-delay="200">
                <div class="flex mb-4 text-yellow-400 text-lg">★★★★★</div>
                <p class="text-gray-700 mb-6 leading-relaxed text-sm sm:text-base">"Le paiement Wave intégré a tout changé. Fini les retards de paiement. Simple, efficace, professionnel."</p>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center font-bold text-blue-600 text-sm">MS</div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm sm:text-base">Moussa Sow</p>
                        <p class="text-xs sm:text-sm text-gray-500">Électronique & Gadgets</p>
                    </div>
                </div>
            </div>

            <div class="testimonial-card bg-white border border-gray-100 rounded-2xl p-6 lg:p-8" data-aos="fade-up" data-aos-delay="300">
                <div class="flex mb-4 text-yellow-400 text-lg">★★★★★</div>
                <p class="text-gray-700 mb-6 leading-relaxed text-sm sm:text-base">"Le rapport qualité-prix est imbattable. Pour 4 900 FCFA/mois, j'ai une vraie boutique en ligne professionnelle. Bravo !"</p>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center font-bold text-green-600 text-sm">FB</div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm sm:text-base">Fatou Badiane</p>
                        <p class="text-xs sm:text-sm text-gray-500">Cosmétiques Naturels</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== HOW IT WORKS ==================== -->
<section class="pt-4 lg:pt-0 pb-20 lg:pb-32 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-primary font-semibold text-sm uppercase tracking-wider bg-primary/10 px-4 py-1.5 rounded-full">Démarrage rapide</span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 mt-4 mb-4">
                En 3 étapes,
                <span class="gradient-text">vous êtes en ligne</span>
            </h2>
        </div>

        <div class="grid sm:grid-cols-3 gap-8 lg:gap-12">
            <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-primary rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6 shadow-lg shadow-primary/30">
                    <span class="text-2xl sm:text-3xl font-black text-white">1</span>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 sm:mb-3">Créez votre compte</h3>
                <p class="text-sm sm:text-base text-gray-600">Inscrivez-vous en 30 secondes. Choisissez le nom de votre boutique et votre lien personnalisé.</p>
            </div>

            <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-primary rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6 shadow-lg shadow-primary/30">
                    <span class="text-2xl sm:text-3xl font-black text-white">2</span>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 sm:mb-3">Ajoutez vos produits</h3>
                <p class="text-sm sm:text-base text-gray-600">Importez vos produits avec photos et prix. Organisez-les en catégories. C'est aussi simple qu'un post Instagram.</p>
            </div>

            <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-primary rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6 shadow-lg shadow-primary/30">
                    <span class="text-2xl sm:text-3xl font-black text-white">3</span>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 sm:mb-3">Partagez votre lien</h3>
                <p class="text-sm sm:text-base text-gray-600">Partagez votre boutique sur WhatsApp, Facebook, Instagram. Recevez des commandes et encaissez vos paiements.</p>
            </div>
        </div>
    </div>
</section>

<!-- ==================== FAQ ==================== -->
<section id="faq" class="pt-4 lg:pt-0 pb-20 lg:pb-32">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-primary font-semibold text-sm uppercase tracking-wider bg-primary/10 px-4 py-1.5 rounded-full">Questions fréquentes</span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 mt-4 mb-4">
                On vous
                <span class="gradient-text">répond</span>
            </h2>
        </div>

        <div class="space-y-3 sm:space-y-4">
            @php
                $faqs = [
                    ['q' => 'Comment fonctionne la période d\'essai gratuite ?', 'a' => 'Vous bénéficiez de 14 jours d\'essai gratuit sur tous nos plans payants. Aucune carte bancaire requise. Vous pouvez annuler à tout moment. Si vous ne passez pas à un plan payant après l\'essai, votre compte repasse automatiquement en plan Gratuit.'],
                    ['q' => 'Est-ce que FatiHad prend une commission sur mes ventes ?', 'a' => '<strong>Jamais.</strong> Vous payez uniquement votre abonnement mensuel ou annuel. Nous ne prenons aucune commission sur vos ventes. 100% de votre chiffre d\'affaires vous revient.'],
                    ['q' => 'Comment configurer les paiements Wave et Orange Money ?', 'a' => 'C\'est très simple. Dans votre tableau de bord, vous entrez vos numéros Wave et Orange Money. Nous générons automatiquement les liens de paiement pour vos clients. Les fonds arrivent directement sur votre compte mobile.'],
                    ['q' => 'Puis-je changer de plan à tout moment ?', 'a' => 'Absolument. Vous pouvez upgrader ou downgrader votre plan à tout moment. La différence est calculée au prorata. Sans engagement, sans frais cachés.'],
                    ['q' => 'Est-ce que ma boutique est visible sur Google ?', 'a' => 'Oui ! Chaque boutique FatiHad est optimisée pour le référencement (SEO). Vos produits peuvent apparaître dans les résultats de recherche Google. Vous pouvez également partager votre lien directement sur vos réseaux sociaux.'],
                ];
            @endphp

            @foreach($faqs as $faq)
                <div class="faq-item bg-white border border-gray-200 rounded-2xl overflow-hidden cursor-pointer" onclick="this.classList.toggle('active')">
                    <div class="flex items-center justify-between p-4 sm:p-5 gap-4">
                        <h3 class="font-semibold text-gray-900 text-sm sm:text-base lg:text-lg">{{ $faq['q'] }}</h3>
                        <span class="faq-icon text-xl sm:text-2xl text-primary font-light flex-shrink-0">+</span>
                    </div>
                    <div class="faq-answer px-4 sm:px-5">
                        <p class="text-gray-600 text-sm sm:text-base">{!! $faq['a'] !!}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <p class="text-gray-600 mb-4 text-sm sm:text-base">Vous avez d'autres questions ?</p>
            <a href="https://wa.me/221700000000" target="_blank" class="inline-flex items-center gap-2 bg-green-500 text-white font-semibold px-6 sm:px-8 py-3 rounded-full hover:bg-green-600 transition text-sm sm:text-base">
                <i class="fab fa-whatsapp text-lg sm:text-xl"></i>
                Contactez-nous sur WhatsApp
            </a>
        </div>
    </div>
</section>

<!-- ==================== CONTACT ==================== -->
<section id="contact" class="pt-4 lg:pt-0 pb-20 lg:pb-32 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-primary font-semibold text-sm uppercase tracking-wider bg-primary/10 px-4 py-1.5 rounded-full">Contactez-nous</span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 mt-4 mb-4">
                Une question ?
                <span class="gradient-text">Écrivez-nous</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">
                Notre équipe vous répond dans l'heure. Par email, WhatsApp ou téléphone.
            </p>
        </div>

        <div class="grid lg:grid-cols-3 gap-8 lg:gap-12">

            {{-- Infos de contact --}}
            <div class="space-y-6" data-aos="fade-right" data-aos-duration="800">
                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-300 flex items-start gap-4">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fab fa-whatsapp text-2xl text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-1">WhatsApp</h3>
                        <p class="text-gray-600 text-sm mb-2">Réponse en moins de 30 min</p>
                        <a href="https://wa.me/221772607977" target="_blank" class="text-primary font-semibold text-sm hover:underline">
                            +221 77 260 79 77
                            <i class="fas fa-arrow-right ml-1 text-xs"></i>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-300 flex items-start gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-envelope text-2xl text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-1">Email</h3>
                        <p class="text-gray-600 text-sm mb-2">Réponse en moins d'1 heure</p>
                        <a href="mailto:hello@FatiHad.com" class="text-primary font-semibold text-sm hover:underline">
                            Contact@FatiHad.com
                            <i class="fas fa-arrow-right ml-1 text-xs"></i>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-300 flex items-start gap-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-phone text-2xl text-orange-600"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-1">Téléphone</h3>
                        <p class="text-gray-600 text-sm mb-2">Lun-Ven • 8h-18h</p>
                        <a href="tel:+221772607977" class="text-primary font-semibold text-sm hover:underline">
                            +221 33 800 00 00
                            <i class="fas fa-arrow-right ml-1 text-xs"></i>
                        </a>
                    </div>
                </div>

                {{-- Réseaux sociaux --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h3 class="font-semibold text-gray-900 mb-3">Suivez-nous</h3>
                    <div class="flex gap-3">
                        <a href="#" class="w-11 h-11 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-primary hover:text-white transition-all duration-300 group">
                            <i class="fab fa-facebook-f text-gray-600 group-hover:text-white"></i>
                        </a>
                        <a href="#" class="w-11 h-11 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-primary hover:text-white transition-all duration-300 group">
                            <i class="fab fa-instagram text-gray-600 group-hover:text-white"></i>
                        </a>
                        <a href="#" class="w-11 h-11 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-primary hover:text-white transition-all duration-300 group">
                            <i class="fab fa-tiktok text-gray-600 group-hover:text-white"></i>
                        </a>
                        <a href="#" class="w-11 h-11 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-primary hover:text-white transition-all duration-300 group">
                            <i class="fab fa-linkedin-in text-gray-600 group-hover:text-white"></i>
                        </a>
                        <a href="#" class="w-11 h-11 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-primary hover:text-white transition-all duration-300 group">
                            <i class="fab fa-youtube text-gray-600 group-hover:text-white"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Formulaire de contact --}}
            <div class="lg:col-span-2" data-aos="fade-left" data-aos-duration="800">
                <div class="bg-white rounded-2xl shadow-sm p-6 sm:p-8 lg:p-10">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Envoyez-nous un message</h3>
                    <p class="text-gray-600 text-sm mb-6">Remplissez le formulaire ci-dessous. Nous vous répondrons dans les plus brefs délais.</p>

                    <form action="" method="POST" class="space-y-5">
                        @csrf

                        <div class="grid sm:grid-cols-2 gap-5">
                            {{-- Nom complet --}}
                            <div>
                                <label for="contact_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nom complet <span class="text-red-400">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    id="contact_name"
                                    required
                                    placeholder="Votre nom"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all duration-300 text-gray-900 placeholder-gray-400 text-sm"
                                >
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="contact_email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Email <span class="text-red-400">*</span>
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    id="contact_email"
                                    required
                                    placeholder="votre@email.com"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all duration-300 text-gray-900 placeholder-gray-400 text-sm"
                                >
                            </div>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-5">
                            {{-- Téléphone --}}
                            <div>
                                <label for="contact_phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Téléphone
                                </label>
                                <div class="flex">
<span class="inline-flex items-center px-3 rounded-l-xl border-2 border-r-0 border-gray-200 bg-gray-50 text-gray-500 text-sm font-medium min-w-[85px] sm:min-w-fit sm:px-3">
    <img src="https://flagcdn.com/w20/sn.png" alt="SN" class="w-5 h-3.5 rounded-sm mr-1.5">
    +221
</span>
                                    <input
                                        type="text"
                                        name="phone"
                                        id="contact_phone"
                                        placeholder="77 123 45 67"
                                        class="flex-1 px-4 py-3 rounded-r-xl border-2 border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all duration-300 text-gray-900 placeholder-gray-400 text-sm"
                                    >
                                </div>
                            </div>

                            {{-- Sujet --}}
                            <div>
                                <label for="contact_subject" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Sujet <span class="text-red-400">*</span>
                                </label>
                                <select
                                    name="subject"
                                    id="contact_subject"
                                    required
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all duration-300 text-gray-900 text-sm cursor-pointer bg-white"
                                >
                                    <option value="" disabled selected class="text-gray-400">Choisissez un sujet</option>
                                    <option value="demo">Demander une démo</option>
                                    <option value="tarif">Question sur les tarifs</option>
                                    <option value="technique">Support technique</option>
                                    <option value="partenariat">Partenariat</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                        </div>

                        {{-- Message --}}
                        <div>
                            <label for="contact_message" class="block text-sm font-semibold text-gray-700 mb-2">
                                Message <span class="text-red-400">*</span>
                            </label>
                            <textarea
                                name="message"
                                id="contact_message"
                                rows="5"
                                required
                                placeholder="Décrivez votre besoin..."
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all duration-300 text-gray-900 placeholder-gray-400 text-sm resize-none"
                            ></textarea>
                        </div>

                        {{-- Checkbox consentement --}}
                        <div class="flex items-start gap-3">
                            <input
                                type="checkbox"
                                name="consent"
                                id="contact_consent"
                                required
                                class="mt-0.5 w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary focus:ring-offset-0 cursor-pointer"
                            >
                            <label for="contact_consent" class="text-sm text-gray-600 cursor-pointer">
                                J'accepte que mes données soient utilisées pour me recontacter, conformément à la
                                <a href="#" class="text-primary hover:underline font-medium">politique de confidentialité</a>.
                            </label>
                        </div>

                        {{-- Submit --}}
                        <button
                            type="submit"
                            class="w-full sm:w-auto bg-primary text-white font-semibold px-8 py-3.5 rounded-xl hover:bg-emerald-600 transition-all duration-300 flex items-center justify-center gap-2 shadow-lg shadow-primary/25 hover:shadow-xl hover:shadow-primary/30 hover:-translate-y-0.5"
                        >
                            <i class="fas fa-paper-plane"></i>
                            Envoyer le message
                        </button>

                        {{-- Délai de réponse --}}
                        <p class="text-xs text-gray-400 flex items-center gap-1.5">
                            <i class="fas fa-clock"></i>
                            Réponse garantie en moins de 2 heures ouvrées
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== CTA FINAL ==================== -->
<section class="gradient-hero py-20 lg:py-32">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white mb-6" data-aos="fade-up">
            Prêt à lancer votre boutique ?
        </h2>
        <p class="text-base sm:text-lg text-white/90 mb-10 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
            Rejoignez plus de 500 commerçants sénégalais qui vendent en ligne avec FatiHad. Gratuit, simple, sans risque.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center" data-aos="fade-up" data-aos-delay="200">
            <a href="{{ route('register') }}" class="bg-white text-primary font-bold text-base sm:text-lg px-8 sm:px-10 py-4 rounded-full hover:shadow-2xl hover:scale-105 transition-all duration-300">
                Créer ma boutique gratuitement
            </a>
        </div>
        <p class="text-white/60 text-sm mt-4">Pas de carte bancaire requise • 14 jours d'essai gratuit</p>
    </div>
</section>

<!-- ==================== FOOTER ==================== -->
<footer class="bg-gray-900 text-white py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 lg:gap-12">
            <div class="col-span-2 md:col-span-1">
                <div class="flex items-center gap-2 mb-4">
                    <div class="   flex items-center justify-center">
                        <img src="{{ asset('images/fatihads.png') }}" alt="FatiHad" class="w-10 h-auto rounded-xl object-cover -mt-2">
                    </div>
                    <span class="text-xl font-bold">FatiHad</span>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed">
                    La solution SaaS pour créer votre boutique en ligne au Sénégal. Simple, rapide, efficace.
                </p>
            </div>
            <div>
                <h4 class="font-semibold mb-4 text-sm sm:text-base">Produit</h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li><a href="#fonctionnalites" class="hover:text-white transition">Fonctionnalités</a></li>
                    <li><a href="#tarifs" class="hover:text-white transition">Tarifs</a></li>
                    <li><a href="#" class="hover:text-white transition">Démonstration</a></li>
                    <li><a href="#" class="hover:text-white transition">Mises à jour</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4 text-sm sm:text-base">Ressources</h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li><a href="#" class="hover:text-white transition">Blog</a></li>
                    <li><a href="#" class="hover:text-white transition">Centre d'aide</a></li>
                    <li><a href="#" class="hover:text-white transition">Guide du commerçant</a></li>
                    <li><a href="#" class="hover:text-white transition">API développeurs</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4 text-sm sm:text-base">Contact</h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li class="flex items-center gap-2"><i class="fab fa-whatsapp text-green-400"></i> +221 77 260 79 77</li>
                    <li class="flex items-center gap-2"><i class="fas fa-envelope"></i> Contact@FatiHad.com</li>
                    <li class="flex items-center gap-2"><i class="fas fa-map-marker-alt"></i> Dakar, Sénégal</li>
                </ul>
                <div class="flex gap-3 mt-4">
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-500 text-xs sm:text-sm">
            <p>&copy; 2026 FatiHad. Tous droits réservés. | <a href="#" class="hover:text-white transition">Conditions générales</a> | <a href="#" class="hover:text-white transition">Politique de confidentialité</a></p>
        </div>
    </div>
</footer>

<!-- ==================== SCRIPTS ==================== -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    // AOS Animation
    AOS.init({
        duration: 800,
        once: true,
        disable: 'mobile'
    });

    // Pricing Toggle
    const toggle = document.getElementById('pricing-toggle');
    const prices = document.querySelectorAll('.monthly-price');
    const knob = toggle.querySelector('span');
    let isYearly = false;

    toggle.addEventListener('click', function() {
        isYearly = !isYearly;

        if (isYearly) {
            knob.style.transform = 'translateX(28px)';
            toggle.classList.add('bg-primary');
            toggle.classList.remove('bg-gray-300');
            document.getElementById('yearly-label').classList.add('text-primary', 'font-bold');
            document.getElementById('monthly-label').classList.remove('text-primary', 'font-bold');
        } else {
            knob.style.transform = 'translateX(4px)';
            toggle.classList.remove('bg-primary');
            toggle.classList.add('bg-gray-300');
            document.getElementById('monthly-label').classList.add('text-primary', 'font-bold');
            document.getElementById('yearly-label').classList.remove('text-primary', 'font-bold');
        }

        prices.forEach(price => {
            const monthly = price.dataset.monthly;
            const yearly = price.dataset.yearly;
            price.textContent = isYearly ? Number(yearly).toLocaleString('fr-FR').replace(/\s/g, ' ') : Number(monthly).toLocaleString('fr-FR').replace(/\s/g, ' ');
        });
    });
</script>
<script>
    function typedText() {
        return {
            texts: ['en 5 minutes', 'sans stress', ' WhatsApp', 'gratuitement'],
            currentIndex: 0,
            displayText: '',
            isDeleting: false,
            charIndex: 0,
            typeSpeed: 200,
            deleteSpeed: 80,
            pauseDelay: 3000,

            init() {
                this.type();
            },

            type() {
                const current = this.texts[this.currentIndex];

                if (!this.isDeleting) {
                    this.displayText = current.substring(0, this.charIndex + 1);
                    this.charIndex++;

                    if (this.charIndex === current.length) {
                        setTimeout(() => {
                            this.isDeleting = true;
                            this.type();
                        }, this.pauseDelay);
                        return;
                    }
                } else {
                    this.displayText = current.substring(0, this.charIndex - 1);
                    this.charIndex--;

                    if (this.charIndex === 0) {
                        this.isDeleting = false;
                        this.currentIndex = (this.currentIndex + 1) % this.texts.length;
                    }
                }

                setTimeout(() => this.type(), this.isDeleting ? this.deleteSpeed : this.typeSpeed);
            }
        }
    }
</script>

</body>
</html>
