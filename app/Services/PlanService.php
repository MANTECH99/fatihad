<?php

namespace App\Services;

class PlanService
{
    public static array $plans = [
        'free' => [
            'name' => 'Gratuit',
            'shops' => 2,
            'products' => 6,
            'price' => 0,
            'features' => ['whatsapp', 'basic_stats'],
        ],
        'starter' => [
            'name' => 'Débutant',
            'shops' => 1,
            'products' => 50,
            'price' => 250,
            'features' => ['whatsapp', 'basic_stats', 'payments', 'customizable'],
        ],
        'business' => [
            'name' => 'Professionnel',
            'shops' => 3,
            'products' => 100,
            'price' => 250,
            'features' => ['whatsapp', 'advanced_stats', 'payments', 'reviews', 'qr_code', 'priority_support'],
        ],
        'enterprise' => [
            'name' => 'Business',
            'shops' => -1,
            'products' => -1,
            'price' => 19900,
            'features' => ['whatsapp', 'advanced_stats', 'payments', 'reviews', 'qr_code', 'priority_support', 'api', 'stock'],
        ],
    ];


    // NOUVEAU : Plans de Certification
    public static array $certifications = [
        'trusted_seller' => [
            'name' => 'Vendeur de Confiance',
            'price' => 140000,
            'duration' => 'an',
            'features' => ['Badge de confiance visible']
        ],
        'entrepreneur' => [
            'name' => 'Entrepreneur Certifié',
            'price' => 250,
            'duration' => 'an',
            'features' => ['Badge certification']
        ],
        'entreprise' => [
            'name' => 'Entreprise Certifiée',
            'price' => 30000,
            'duration' => 'an',
            'features' => ['Badge officiel']
        ],
    ];
    // 🔥 NOUVEAU : Plans d'accès à la Marketplace (3 niveaux)
    public static array $marketplacePlans = [
        'marketplace_basic' => [
            'name' => 'Marketplace Basic',
            'price' => 250,
            'duration' => 'mois',
            'max_products' => 5, // Limite 10 produits
            'features' => [
                'Publier jusqu\'à 10 produits',
                'Importer des produits du catalogue',
                'Gagner des commissions sur les reventes',
                'Badge certifié Marketplace'
            ]
        ],
        'marketplace_plus' => [
            'name' => 'Marketplace Plus',
            'price' => 500,
            'duration' => 'mois',
            'max_products' => 20, // Limite 20 produits
            'features' => [
                'Publier jusqu\'à 20 produits',
                'Importer des produits du catalogue',
                'Gagner des commissions sur les reventes',
                'Badge certifié Marketplace',
                'Visibilité renforcée'
            ]
        ],
        'marketplace_premium' => [
            'name' => 'Marketplace Premium',
            'price' => 140000,
            'duration' => 'mois',
            'max_products' => -1, // Illimité (-1)
            'features' => [
                'Publier un nombre illimité de produits',
                'Importer des produits du catalogue',
                'Gagner des commissions sur les reventes',
                'Badge certifié Marketplace',
                'Visibilité premium',
                'Support prioritaire'
            ]
        ]
    ];

    public static function get(string $plan): array
    {
        return self::$plans[$plan] ?? self::$plans['free'];
    }

    public static function canCreateShop($user): bool
    {
        $plan = self::get($user->plan ?? 'free');
        if ($plan['shops'] === -1) return true;
        return $user->shops()->count() < $plan['shops'];
    }

    public static function canAddProduct($user, $shop): bool
    {
        $plan = self::get($user->plan ?? 'free');
        if ($plan['products'] === -1) return true;
        return $shop->products()->count() < $plan['products'];
    }

    public static function hasFeature($user, string $feature): bool
    {
        $plan = self::get($user->plan ?? 'free');
        return in_array($feature, $plan['features']);
    }
}
