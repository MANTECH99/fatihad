<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->string('wave_number')->nullable()->after('whatsapp_phone');
            $table->string('orange_money_number')->nullable()->after('wave_number');
            $table->string('payout_method')->default('wave')->after('orange_money_number');
        });
    }

    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('wave_number');
            $table->dropColumn('orange_money_number');
            $table->dropColumn('payout_method');
        });
    }
};
