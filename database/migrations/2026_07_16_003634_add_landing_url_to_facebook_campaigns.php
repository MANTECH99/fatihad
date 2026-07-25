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
        Schema::table('facebook_campaigns', function (Blueprint $table) {
            $table->string('landing_url')->nullable()->after('product_id');
            $table->string('post_message')->nullable()->after('landing_url');
            $table->string('post_image')->nullable()->after('post_message');
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
