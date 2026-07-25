{{-- resources/views/storefront/pending.blade.php --}}
    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $shop->name }} - En attente</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center p-4">
<div class="">
    <!-- Carte principale -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Bannière colorée -->
        <div class="bg-gradient-to-r from-amber-400 via-orange-400 to-amber-500 h-3"></div>

        <div class="p-8">
            <!-- Icône animée -->
            <div class="flex justify-center mb-6">
                <div class="relative">
                    <div class="w-24 h-24 bg-amber-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-store-alt text-4xl text-amber-500"></i>
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-8 h-8 bg-amber-400 rounded-full flex items-center justify-center animate-pulse">
                        <i class="fas fa-clock text-white text-sm"></i>
                    </div>
                </div>
            </div>

            <!-- Nom boutique -->
            <div class="flex items-center justify-center gap-2 mb-2">
                @if($shop->logo_url)
                    <img src="{{ $shop->logo_url }}" class="w-8 h-8 rounded-full object-cover">
                @endif
                <h1 class="text-xl font-bold text-gray-900">{{ $shop->name }}</h1>
            </div>

            @if($shop->city)
                <p class="text-center text-sm text-gray-500 mb-4">
                    <i class="fas fa-map-marker-alt text-amber-400 mr-1"></i> {{ $shop->city }}
                </p>
            @endif

            @if($isOwner)
                <!-- Message propriétaire -->
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mb-6">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-hourglass-half text-amber-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">
                                @if($shop->status === 'pending')
                                    En cours de validation
                                @elseif($shop->status === 'rejected')
                                    Boutique refusée
                                @else
                                    En attente d'activation
                                @endif
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                @if($shop->status === 'pending')
                                    Notre équipe vérifie votre boutique. Cela prend généralement moins de 24h.
                                @elseif($shop->status === 'rejected')
                                    Votre boutique n'a pas été validée. Contactez le support pour plus d'informations.
                                @else
                                    Activez votre boutique pour la rendre visible.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Étapes -->
                <div class="space-y-3 mb-6">
                    <div class="flex items-center gap-3 text-sm">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check text-green-500 text-xs"></i>
                        </div>
                        <span class="text-gray-600">Compte créé</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check text-green-500 text-xs"></i>
                        </div>
                        <span class="text-gray-600">Boutique configurée</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <div class="w-6 h-6 {{ $shop->status === 'approved' ? 'bg-green-100' : 'bg-amber-100' }} rounded-full flex items-center justify-center flex-shrink-0 {{ $shop->status !== 'approved' ? 'animate-pulse' : '' }}">
                            @if($shop->status === 'approved')
                                <i class="fas fa-check text-green-500 text-xs"></i>
                            @else
                                <i class="fas fa-clock text-amber-500 text-xs"></i>
                            @endif
                        </div>
                        <span class="text-gray-600">Validation par l'équipe</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-2">
                    <a href="{{ route('merchant.shops.edit', $shop) }}"
                       class="flex items-center justify-center gap-2 w-full bg-gradient-to-r from-emerald-500 to-emerald-600 text-white py-3 rounded-xl font-medium hover:from-emerald-600 hover:to-emerald-700 transition shadow-md shadow-emerald-200">
                        <i class="fas fa-edit"></i> Modifier ma boutique
                    </a>
                    <a href="{{ route('merchant.dashboard') }}"
                       class="flex items-center justify-center gap-2 w-full border-2 border-gray-200 text-gray-700 py-3 rounded-xl font-medium hover:bg-gray-50 transition">
                        <i class="fas fa-tachometer-alt"></i> Tableau de bord
                    </a>
                </div>
            @else
                <!-- Message visiteur -->
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mb-6">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-store-slash text-amber-500 text-lg"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">Boutique indisponible</h3>
                        <p class="text-sm text-gray-600">
                            Le commerçant finalise la configuration de sa boutique.<br>
                            Revenez bientôt !
                        </p>
                    </div>
                </div>

                <a href="{{ url('/') }}"
                   class="flex items-center justify-center gap-2 w-full bg-gradient-to-r from-emerald-500 to-emerald-600 text-white py-3 rounded-xl font-medium hover:from-emerald-600 hover:to-emerald-700 transition shadow-md shadow-emerald-200">
                    <i class="fas fa-home"></i> Retour à l'accueil
                </a>
            @endif
        </div>
    </div>

    <!-- Pied de page -->
    <p class="text-center text-sm text-gray-400 mt-6">
        Propulsé par <span class="font-semibold text-gray-500">Seneshop</span>
    </p>
</div>
</body>
</html>
