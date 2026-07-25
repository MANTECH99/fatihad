<?php
// database/migrations/2026_07_14_170321_add_facebook_post_id_to_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('facebook_post_id')->nullable()->after('supplier');
            // Ou utilisez 'after(\'track_inventory\')' ou 'after(\'cost_price\')'
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('facebook_post_id');
        });
    }
};
