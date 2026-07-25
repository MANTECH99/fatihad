@extends('merchant.layouts.app')

@section('title', 'Paiement en cours')

@section('content')
    <div class="flex justify-center items-center h-screen bg-gray-50">
        <div class="bg-white p-8 rounded-2xl shadow-lg text-center max-w-md border border-gray-100">
            <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 text-orange-500 text-4xl">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Paiement en attente...</h2>
            <p class="text-gray-500 mb-6">Veuillez attendre la finalisation de votre paiement.</p>
            <p class="text-sm text-gray-400">Référence: {{ $externalId }}</p>
        </div>
    </div>

    <script>
        const checkStatus = () => {
            fetch("{{ route('certification.check-status', $externalId) }}")
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        setTimeout(checkStatus, 3000); // Re-check toutes les 3 secondes
                    }
                })
                .catch(() => setTimeout(checkStatus, 5000));
        };
        setTimeout(checkStatus, 2000);
    </script>
@endsection
