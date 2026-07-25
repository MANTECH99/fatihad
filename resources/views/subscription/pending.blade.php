@extends('merchant.layouts.app')

@section('title', 'Paiement en cours')
@section('header', 'Paiement en cours')

@section('content')
    <div class="max-w-lg mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border p-8 text-center">

            {{-- Animation de chargement --}}
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-emerald-100 rounded-full">
                    <svg class="animate-spin h-10 w-10 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            <h2 class="text-xl font-bold text-gray-900 mb-2">Paiement en attente</h2>

            <p class="text-gray-500 mb-6">
                @if($pendingMethod === 'orange_money')
                    Validez le paiement en composant le <strong class="text-orange-600">#144#</strong> sur votre téléphone.
                @else
                    Validez le paiement dans votre application <strong class="text-blue-600">Wave</strong>.
                @endif
            </p>

            {{-- Montant --}}
            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <p class="text-sm text-gray-500 mb-1">Montant à payer</p>
                <p class="text-3xl font-black text-gray-900">
                    {{ number_format($pendingTotalPaid, 0, ',', ' ') }}
                    <span class="text-lg font-normal text-gray-400">FCFA</span>
                </p>
            </div>

            {{-- Instructions selon le mode de paiement --}}
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-left">
                <h3 class="font-semibold text-amber-800 mb-2 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i> Instructions
                </h3>
                <ul class="text-sm text-amber-700 space-y-2">
                    @if($pendingMethod === 'orange_money')
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5">1.</span>
                            <span>Composez <strong>#144#</strong> sur votre téléphone</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5">2.</span>
                            <span>Sélectionnez <strong>"Paiement marchand"</strong></span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5">3.</span>
                            <span>Entrez votre code secret pour valider</span>
                        </li>
                    @else
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5">1.</span>
                            <span>Ouvrez votre application <strong>Wave</strong></span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5">2.</span>
                            <span>Validez la demande de paiement reçue</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5">3.</span>
                            <span>Entrez votre code PIN Wave</span>
                        </li>
                    @endif
                    <li class="flex items-start gap-2">
                        <span class="text-amber-500 mt-0.5">4.</span>
                        <span>Revenez sur cette page, la validation est automatique</span>
                    </li>
                </ul>
            </div>

            {{-- Barre de progression --}}
            <div class="w-full bg-gray-200 rounded-full h-2 mb-6 overflow-hidden">
                <div id="progress-bar" class="bg-emerald-500 h-2 rounded-full transition-all duration-1000" style="width: 0%"></div>
            </div>

            <p id="status-text" class="text-sm text-gray-500 mb-4">
                <span class="inline-block w-2 h-2 bg-yellow-400 rounded-full animate-pulse mr-2"></span>
                En attente de validation...
            </p>

            {{-- Bouton annuler --}}
            <a href="{{ route('subscription.index') }}" class="block w-full bg-gray-100 text-gray-700 py-3 px-4 rounded-xl hover:bg-gray-200 transition font-medium text-center">
                Annuler et revenir
            </a>
        </div>
    </div>

    <script>
        let checkCount = 0;
        const maxChecks = 60;
        let isChecking = false;

        const checkUrl = '{{ route("certification.check-status", ["externalId" => $externalId]) }}';
        const indexUrl = '{{ route("certification.index") }}';

        function updateProgress() {
            const progressBar = document.getElementById('progress-bar');
            if (progressBar) {
                const percentage = Math.min((checkCount / maxChecks) * 100, 100);
                progressBar.style.width = percentage + '%';
            }
        }

        async function checkPaymentStatus() {
            if (isChecking) return;
            isChecking = true;

            try {
                const response = await fetch(checkUrl);
                const data = await response.json();

                if (data.success && data.status === 'SUCCESS') {
                    document.getElementById('status-text').innerHTML = '<span class="inline-block w-2 h-2 bg-green-400 rounded-full mr-2"></span>Paiement validé ! Redirection...';
                    setTimeout(() => { window.location.href = data.redirect; }, 1500);
                    return true;
                }

                if (data.status === 'FAILED') {
                    document.getElementById('status-text').innerHTML = '<span class="inline-block w-2 h-2 bg-red-400 rounded-full mr-2"></span>Paiement échoué';
                    setTimeout(() => { window.location.href = indexUrl; }, 2000);
                    return true;
                }

                checkCount++;
                updateProgress();

                if (checkCount < maxChecks) {
                    setTimeout(checkPaymentStatus, 5000);
                } else {
                    document.getElementById('status-text').innerHTML = '<span class="inline-block w-2 h-2 bg-red-400 rounded-full mr-2"></span>Délai dépassé. Veuillez réessayer.';
                }

            } catch (error) {
                console.error('Erreur vérification:', error);
            }

            isChecking = false;
        }

        // Auto-check après 3 secondes
        setTimeout(checkPaymentStatus, 3000);
    </script>
@endsection
