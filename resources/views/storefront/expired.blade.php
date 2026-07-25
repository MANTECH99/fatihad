{{-- resources/views/storefront/expired.blade.php --}}
    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $shop->name }} - Indisponible</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
<div class="max-w-md w-full text-center">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-clock text-2xl text-red-500"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $shop->name }}</h2>
        <p class="text-gray-500 mb-6">Cette boutique est temporairement indisponible.</p>
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
            <i class="fas fa-info-circle mr-1"></i>
            Le commerçant n'a pas encore activé son abonnement.
        </div>
    </div>
</div>
</body>
</html>
