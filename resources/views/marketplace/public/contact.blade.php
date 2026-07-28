@extends('layouts.marketplace')

@section('title', 'Contact - Seneshop')

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
            <span class="text-gray-900 font-medium">Contact</span>
        </nav>

        {{-- HERO --}}
        <div class="relative rounded-2xl overflow-hidden shadow-lg mb-12">
            <img src="{{ asset('images/contact-ban.png') }}" alt="Contactez-nous" class="w-full h-auto object-contain">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            {{-- Téléphone --}}
            <div class="bg-white rounded-xl shadow-sm p-8 text-center border border-gray-100 hover:shadow-md transition">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-phone text-2xl text-emerald-600"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Par téléphone</h3>
                <p class="text-gray-500 text-sm mb-4">Disponible du lundi au samedi<br>de 9h à 19h</p>
                <a href="tel:+221772607977" class="text-emerald-600 font-bold text-xl hover:text-emerald-700 transition">77 260 79 77</a>
            </div>

            {{-- WhatsApp --}}
            <div class="bg-white rounded-xl shadow-sm p-8 text-center border border-gray-100 hover:shadow-md transition">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fab fa-whatsapp text-2xl text-green-600"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Par WhatsApp</h3>
                <p class="text-gray-500 text-sm mb-4">Échangez directement avec nous<br>réponse rapide garantie</p>
                <a href="https://wa.me/221772607977" target="_blank" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-green-600 text-white font-bold px-6 py-3 rounded-full transition">
                    <i class="fab fa-whatsapp"></i> Discuter sur WhatsApp
                </a>
            </div>

            {{-- Adresse --}}
            <div class="bg-white rounded-xl shadow-sm p-8 text-center border border-gray-100 hover:shadow-md transition">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map-marker-alt text-2xl text-indigo-600"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Notre adresse</h3>
                <p class="text-gray-500 text-sm mb-4">Venez nous rendre visite<br>à Dakar</p>
                <p class="text-gray-900 font-bold">GUEULE TAPÉE</p>
                <p class="text-gray-500 text-sm">Dakar, Sénégal</p>
            </div>
        </div>

        {{-- FAQ RAPIDE --}}
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-8 md:p-12 mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 text-center mb-8">
                Questions <span class="text-emerald-500">fréquentes</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-question-circle text-emerald-500"></i> Comment créer ma boutique ?
                    </h3>
                    <p class="text-sm text-gray-500">Inscrivez-vous gratuitement, remplissez vos informations et commencez à ajouter vos produits. C'est simple et rapide.</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-question-circle text-emerald-500"></i> Est-ce gratuit de vendre ?
                    </h3>
                    <p class="text-sm text-gray-500">L'inscription est gratuite. Nous prélevons aucune commission  sur les ventes réalisées.</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-question-circle text-emerald-500"></i> Comment sont gérés les paiements ?
                    </h3>
                    <p class="text-sm text-gray-500">Les paiements sont gérés directement entre vous et vos clients. Vous gardez le contrôle total.</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-question-circle text-emerald-500"></i> Comment contacter le support ?
                    </h3>
                    <p class="text-sm text-gray-500">Par téléphone au 77 260 79 77, par WhatsApp ou en passant à notre bureau à Gueule Tapée.</p>
                </div>
            </div>
        </div>

        {{-- CTA --}}
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl shadow-lg p-8 md:p-12 text-center text-white">
            <h2 class="text-2xl md:text-3xl font-bold mb-4">Toujours une question ?</h2>
            <p class="text-white/80 text-lg mb-8">Notre équipe est là pour vous répondre.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="https://wa.me/221772607977" target="_blank" class="inline-flex items-center justify-center gap-2 bg-white text-emerald-600 font-bold px-8 py-4 rounded-full hover:bg-gray-100 transition shadow-lg text-lg">
                    <i class="fab fa-whatsapp"></i> Nous écrire sur WhatsApp
                </a>
                <a href="tel:+221772607977" class="inline-flex items-center justify-center gap-2 bg-white/20 backdrop-blur-sm text-white font-bold px-8 py-4 rounded-full hover:bg-white/30 transition border border-white/30 text-lg">
                    <i class="fas fa-phone"></i> Nous appeler
                </a>
            </div>
        </div>
    </div>
@endsection
