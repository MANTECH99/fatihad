<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('certifications', function (Blueprint $table) {
            // Ajoute la colonne shop_id (nullable car une certification peut être liée à un user sans shop précis)
            $table->foreignId('shop_id')
                ->nullable()
                ->constrained('shops')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->dropColumn('shop_id');
        });
    }
};
