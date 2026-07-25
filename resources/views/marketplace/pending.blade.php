@extends('merchant.layouts.app')

@section('title', 'Paiement en cours - Marketplace')

@section('content')
    <div class="flex justify-center items-center h-screen bg-gray-50">
        <div class="bg-white p-8 rounded-2xl shadow-lg text-center max-w-md border border-gray-100">

            <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4 text-indigo-500 text-4xl">
                <i class="fas fa-spinner fa-spin"></i>
            </div>

            <h2 class="text-2xl font-bold mb-2">Paiement en attente...</h2>

            <p class="text-gray-500 mb-4">
                Veuillez finaliser le paiement sur votre téléphone via l'interface Dexpay.
            </p>

            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 mb-6 text-left">
                <p class="text-sm text-indigo-800 font-medium mb-1">🛡️ Paiement sécurisé</p>
                <p class="text-xs text-indigo-600">
                    Une fois le paiement validé, votre accès à la Marketplace sera activé instantanément.
                </p>
            </div>

            <p class="text-sm text-gray-400 mb-4">Référence: {{ $externalId }}</p>

            <div class="w-full bg-gray-200 rounded-full h-1.5 mb-4 overflow-hidden">
                <div id="progress-bar" class="bg-indigo-500 h-1.5 rounded-full transition-all duration-1000" style="width: 0%"></div>
            </div>

            <p id="status-text" class="text-sm text-gray-500">
                <span class="inline-block w-2 h-2 bg-yellow-400 rounded-full animate-pulse mr-2"></span>
                En attente de validation...
            </p>
        </div>
    </div>

    <script>
        let checkCount = 0;
        const maxChecks = 60; // 60 * 3 secondes = 3 minutes max
        let isChecking = false;

        const checkUrl = '{{ route("marketplace.check-status", $externalId) }}';
        const statusUrl = '{{ route("marketplace.status") }}';
        const indexUrl = '{{ route("marketplace.index") }}';

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
                    document.getElementById('status-text').innerHTML =
                        '<span class="inline-block w-2 h-2 bg-green-400 rounded-full mr-2"></span>Paiement validé ! Redirection...';
                    setTimeout(() => {
                        window.location.href = data.redirect ?? statusUrl;
                    }, 1500);
                    return true;
                }

                if (data.status === 'FAILED') {
                    document.getElementById('status-text').innerHTML =
                        '<span class="inline-block w-2 h-2 bg-red-400 rounded-full mr-2"></span>Paiement échoué';
                    setTimeout(() => {
                        window.location.href = indexUrl;
                    }, 2000);
                    return true;
                }

                checkCount++;
                updateProgress();

                if (checkCount < maxChecks) {
                    setTimeout(checkPaymentStatus, 3000); // Re-check toutes les 3 secondes
                } else {
                    document.getElementById('status-text').innerHTML =
                        '<span class="inline-block w-2 h-2 bg-red-400 rounded-full mr-2"></span>Délai dépassé. Veuillez réessayer.';
                    setTimeout(() => {
                        window.location.href = indexUrl;
                    }, 3000);
                }

            } catch (error) {
                console.error('Erreur vérification:', error);
                setTimeout(checkPaymentStatus, 5000);
            }

            isChecking = false;
        }

        // Auto-check après 2 secondes
        setTimeout(checkPaymentStatus, 2000);
    </script>
@endsection
