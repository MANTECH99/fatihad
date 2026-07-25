<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Shop;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Créer l'admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@seneshop.sn'],
            [
                'name' => 'Admin Seneshop',
                'phone' => '+221770000000',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Créer un commerçant de test
        $merchant = User::firstOrCreate(
            ['email' => 'fatou@example.sn'],
            [
                'name' => 'Fatou Diop',
                'phone' => '+221771234567',
                'password' => Hash::make('password'),
                'role' => 'merchant',
                'email_verified_at' => now(),
            ]
        );

        // 3. Créer une boutique pour le commerçant
        $shop = Shop::firstOrCreate(
            ['slug' => 'chez-fatou-dakar'],
            [
                'user_id' => $merchant->id,
                'name' => 'Chez Fatou',
                'description' => 'Spécialités sénégalaises faites maison',
                'whatsapp_phone' => '+221771234567',
                'city' => 'Dakar',
                'address' => 'Yoff, près de la mosquée',
                'delivery_zones' => ['Yoff', 'Almadies', 'Ngor'],
                'delivery_fee' => 1000,
                'min_order' => 3000,
                'currency' => 'XOF',
                'is_open' => true,
                'is_active' => true,
                'status' => 'approved',
            ]
        );

        // 4. Créer des catégories
        $category1 = Category::create([
            'shop_id' => $shop->id,
            'name' => 'Plats principaux',
            'order' => 1,
            'is_active' => true,
        ]);

        $category2 = Category::create([
            'shop_id' => $shop->id,
            'name' => 'Boissons',
            'order' => 2,
            'is_active' => true,
        ]);

        // 5. Créer des produits
        Product::create([
            'shop_id' => $shop->id,
            'category_id' => $category1->id,
            'name' => 'Thieb bou dien (Riz au poisson)',
            'description' => 'Riz parfumé au poisson frais, légumes et sauce tomate',
            'price' => 2500,
            'sale_price' => 2000,
            'is_available' => true,
            'is_featured' => true,
            'order' => 1,
            'options' => [
                [
                    'name' => 'Taille',
                    'values' => ['Petite portion', 'Grande portion'],
                    'prices' => [0, 1000]
                ],
                [
                    'name' => 'Supplément',
                    'values' => ['Normal', 'Avec crevettes'],
                    'prices' => [0, 1500]
                ]
            ],
        ]);

        Product::create([
            'shop_id' => $shop->id,
            'category_id' => $category1->id,
            'name' => 'Yassa poulet',
            'description' => 'Poulet mariné au citron et oignons confits',
            'price' => 3000,
            'is_available' => true,
            'is_featured' => true,
            'order' => 2,
        ]);

        Product::create([
            'shop_id' => $shop->id,
            'category_id' => $category1->id,
            'name' => 'Mafé',
            'description' => 'Sauce à la pâte d\'arachide avec viande tendre',
            'price' => 2800,
            'is_available' => true,
            'order' => 3,
        ]);

        Product::create([
            'shop_id' => $shop->id,
            'category_id' => $category2->id,
            'name' => 'Bissap',
            'description' => 'Jus de fleurs d\'hibiscus frais',
            'price' => 500,
            'is_available' => true,
            'order' => 1,
        ]);

        Product::create([
            'shop_id' => $shop->id,
            'category_id' => $category2->id,
            'name' => 'Gingembre',
            'description' => 'Jus de gingembre piquant et sucré',
            'price' => 500,
            'is_available' => true,
            'order' => 2,
        ]);

        // 6. Paramètres par défaut
        Setting::firstOrCreate(['key' => 'site_name'], ['value' => 'Seneshop', 'group' => 'general']);
        Setting::firstOrCreate(['key' => 'currency'], ['value' => 'XOF', 'group' => 'general']);
        Setting::firstOrCreate(['key' => 'timezone'], ['value' => 'Africa/Dakar', 'group' => 'general']);


    }
}
