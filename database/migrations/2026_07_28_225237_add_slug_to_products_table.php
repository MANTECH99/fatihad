<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Product;

return new class extends Migration
{
    public function up()
    {
        // 1. Ajouter la colonne avec une taille max de 191 (191 × 4 = 764 < 1000)
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug', 191)->nullable()->after('name');
        });

        // 2. Générer les slugs pour les produits existants
        Product::chunk(100, function ($products) {
            foreach ($products as $product) {
                $slug = Str::slug($product->name ?: 'produit-' . $product->id);
                $originalSlug = $slug;
                $count = 1;

                while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }

                // Limiter à 191 caractères
                if (strlen($slug) > 191) {
                    $slug = substr($slug, 0, 185) . '-' . $product->id;
                }

                $product->slug = $slug;
                $product->save();
            }
        });

        // 3. Ajouter l'index unique
        Schema::table('products', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
