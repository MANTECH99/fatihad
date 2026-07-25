
    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conditions Générales d'Utilisation - Seneshop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50">
<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-sm p-8 lg:p-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Conditions Générales d'Utilisation</h1>
        <p class="text-gray-500 mb-8">Dernière mise à jour : {{ date('d/m/Y') }}</p>

        <div class="space-y-8 text-gray-700 leading-relaxed">
            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">1. Présentation</h2>
                <p>Seneshop est une plateforme SaaS éditée par <strong>Mantech</strong>, permettant aux commerçants de créer et gérer leur boutique en ligne.</p>
                <p class="mt-2">L'utilisation de la plateforme implique l'acceptation pleine et entière des présentes conditions générales d'utilisation.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">2. Services</h2>
                <p>Seneshop propose les services suivants :</p>
                <ul class="list-disc pl-6 mt-2 space-y-1">
                    <li>Création de boutique en ligne personnalisée</li>
                    <li>Gestion de catalogue produits</li>
                    <li>Réception de commandes via WhatsApp</li>
                    <li>Paiements via Wave et Orange Money</li>
                    <li>Publication automatique sur Facebook</li>
                    <li>Campagnes publicitaires (boost et retargeting)</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">3. Inscription et compte</h2>
                <p>L'utilisateur doit fournir des informations exactes lors de son inscription. Il est responsable de la confidentialité de ses identifiants.</p>
                <p class="mt-2">Seneshop se réserve le droit de suspendre ou supprimer tout compte ne respectant pas les présentes conditions.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">4. Abonnements et paiements</h2>
                <p>Seneshop propose différents plans tarifaires. Les prix sont affichés en FCFA.</p>
                <ul class="list-disc pl-6 mt-2 space-y-1">
                    <li>Plan Gratuit : 0 FCFA/mois</li>
                    <li>Plan Débutant : 4 900 FCFA/mois</li>
                    <li>Plan Professionnel : 9 900 FCFA/mois</li>
                    <li>Plan Business : 19 900 FCFA/mois</li>
                </ul>
                <p class="mt-2">Les paiements sont traités via Wave et Orange Money. Aucune commission n'est prélevée sur les ventes des commerçants.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">5. Propriété intellectuelle</h2>
                <p>La plateforme Seneshop, son code source, son design et son nom sont la propriété exclusive de Mantech.</p>
                <p class="mt-2">Les commerçants restent propriétaires de leurs contenus (produits, images, descriptions).</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">6. Responsabilités</h2>
                <p>Seneshop s'engage à fournir un service de qualité mais ne peut être tenu responsable :</p>
                <ul class="list-disc pl-6 mt-2 space-y-1">
                    <li>Des interruptions temporaires du service</li>
                    <li>Du contenu publié par les commerçants</li>
                    <li>Des transactions entre commerçants et clients</li>
                    <li>Des problèmes liés aux services tiers (Facebook, Wave, Orange Money)</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">7. Protection des données</h2>
                <p>Les données personnelles sont traitées conformément à notre <a href="/politique-confidentialite" class="text-emerald-600 hover:underline">Politique de Confidentialité</a>.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">8. Résiliation</h2>
                <p>L'utilisateur peut résilier son abonnement à tout moment. La résiliation prend effet à la fin de la période en cours.</p>
                <p class="mt-2">Seneshop se réserve le droit de résilier un compte en cas de violation des présentes conditions.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">9. Loi applicable</h2>
                <p>Les présentes conditions sont régies par le droit sénégalais. Tout litige sera soumis aux tribunaux compétents de Dakar.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">10. Contact</h2>
                <p>Pour toute question :</p>
                <ul class="list-disc pl-6 mt-2 space-y-1">
                    <li>Email : contact@seneshop.com</li>
                    <li>WhatsApp : +221 77 260 79 77</li>
                    <li>Adresse : Dakar, Sénégal</li>
                </ul>
            </section>
        </div>

        <div class="mt-12 pt-8 border-t border-gray-200 text-center">
            <a href="{{ url('/') }}" class="text-emerald-600 hover:underline">
                <i class="fas fa-arrow-left mr-2"></i>Retour à l'accueil
            </a>
        </div>
    </div>
</div>
</body>
</html>
