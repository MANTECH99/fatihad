<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politique de confidentialité - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="max-w-3xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Politique de confidentialité</h1>

    <p class="text-gray-600 mb-4">Dernière mise à jour : {{ date('d/m/Y') }}</p>

    <div class="space-y-6 text-gray-700">
        <section>
            <h2 class="text-xl font-semibold mb-3">1. Collecte des données</h2>
            <p>Nous collectons les informations que vous nous fournissez directement : nom, numéro de téléphone, adresse email, adresse de livraison. Ces données sont nécessaires pour traiter vos commandes.</p>
        </section>

        <section>
            <h2 class="text-xl font-semibold mb-3">2. Utilisation des données</h2>
            <p>Vos données sont utilisées exclusivement pour :</p>
            <ul class="list-disc pl-6 mt-2 space-y-1">
                <li>Traiter et livrer vos commandes</li>
                <li>Communiquer avec vous concernant votre commande</li>
                <li>Améliorer nos services</li>
            </ul>
        </section>

        <section>
            <h2 class="text-xl font-semibold mb-3">3. Paiements</h2>
            <p>Les paiements sont traités via Wave et Orange Money. Nous ne stockons aucune information bancaire. Les transactions sont sécurisées par nos partenaires de paiement.</p>
        </section>

        <section>
            <h2 class="text-xl font-semibold mb-3">4. Partage des données</h2>
            <p>Vos données sont partagées uniquement avec le commerçant auprès duquel vous passez commande. Nous ne vendons ni ne partageons vos données avec des tiers.</p>
        </section>

        <section>
            <h2 class="text-xl font-semibold mb-3">5. Conservation des données</h2>
            <p>Nous conservons vos données aussi longtemps que nécessaire pour fournir nos services ou pour respecter nos obligations légales.</p>
        </section>

        <section>
            <h2 class="text-xl font-semibold mb-3">6. Vos droits</h2>
            <p>Vous avez le droit d'accéder, de modifier ou de supprimer vos données personnelles. Pour toute demande, contactez-nous.</p>
        </section>

        <section>
            <h2 class="text-xl font-semibold mb-3">7. Contact</h2>
            <p>Pour toute question concernant cette politique :</p>
            <p class="mt-2">Email : contact@billeteriexpress.com</p>
        </section>
    </div>

    <p class="mt-8 text-sm text-gray-500">© {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
</div>
</body>
</html>
