<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('facebook_campaigns', function (Blueprint $table) {
            $table->decimal('ctr', 8, 4)->default(0)->after('spent');
            $table->decimal('cpc', 10, 2)->default(0)->after('ctr');
            $table->decimal('cpp', 10, 2)->default(0)->after('cpc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facebook_campaigns', function (Blueprint $table) {
            //
        });
    }
};
