<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            // Supprimer l'ancienne colonne delivery_zones
            $table->dropColumn('delivery_zones');
            // Supprimer l'ancienne colonne delivery_fee
            $table->dropColumn('delivery_fee');
        });

        Schema::table('shops', function (Blueprint $table) {
            // Nouvelle colonne JSON pour stocker les zones avec leurs prix
            $table->json('delivery_zones')->nullable()->after('address');
            // Frais de livraison par défaut
            $table->decimal('delivery_fee', 10, 2)->default(0)->after('delivery_zones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
