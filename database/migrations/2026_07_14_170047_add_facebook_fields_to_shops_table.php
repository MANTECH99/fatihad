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
            $table->string('facebook_page_id')->nullable()->after('stamp');
            $table->text('facebook_access_token')->nullable()->after('facebook_page_id');
            $table->string('facebook_page_name')->nullable()->after('facebook_access_token');
            $table->timestamp('facebook_connected_at')->nullable()->after('facebook_page_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            //
        });
    }
};
