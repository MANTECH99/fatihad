<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppression des données - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50">
<div class="max-w-3xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Suppression de vos données</h1>

    <div class="space-y-6 text-gray-700">
        <section>
            <h2 class="text-xl font-semibold mb-3">Comment demander la suppression de vos données</h2>
            <p>Conformément à la réglementation sur la protection des données, vous pouvez demander la suppression de vos données personnelles à tout moment.</p>
        </section>

        <section>
            <h2 class="text-xl font-semibold mb-3">Méthodes de suppression</h2>

            <div class="space-y-4 mt-4">
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <h3 class="font-semibold text-lg mb-2">Option 1 : Suppression automatique</h3>
                    <p>Connectez-vous à votre compte et allez dans <strong>Paramètres → Confidentialité → Supprimer mes données</strong>.</p>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <h3 class="font-semibold text-lg mb-2">Option 2 : Par email</h3>
                    <p>Envoyez un email à <strong>privacy@billeteriexpress.com</strong> avec :</p>
                    <ul class="list-disc pl-6 mt-2 space-y-1">
                        <li>Votre nom complet</li>
                        <li>L'adresse email associée à votre compte</li>
                        <li>La mention "Demande de suppression de données" en objet</li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <h3 class="font-semibold text-lg mb-2">Option 3 : Via WhatsApp</h3>
                    <p>Contactez-nous au <strong>+221 77 260 79 77</strong> avec votre demande de suppression.</p>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-xl font-semibold mb-3">Délai de traitement</h2>
            <p>Votre demande sera traitée dans un délai maximum de <strong>30 jours</strong>. Vous recevrez une confirmation une fois la suppression effectuée.</p>
        </section>

        <section>
            <h2 class="text-xl font-semibold mb-3">Données supprimées</h2>
            <p>La suppression concerne :</p>
            <ul class="list-disc pl-6 mt-2 space-y-1">
                <li>Votre profil utilisateur</li>
                <li>Votre historique de commandes</li>
                <li>Vos informations de contact</li>
            </ul>
            <p class="mt-3 text-sm text-gray-500">Note : Certaines données peuvent être conservées pour des obligations légales (factures, comptabilité).</p>
        </section>
    </div>

    <p class="mt-8 text-sm text-gray-500">© {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
</div>
</body>
</html>
