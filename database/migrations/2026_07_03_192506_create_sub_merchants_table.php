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
        Schema::create('sub_merchants', function (Blueprint $table) {
            $table->id();
            $table->string('sub_merchant_id')->unique();
            $table->string('name');
            $table->string('commercial_name');
            $table->string('site')->comment('disso ou caravane');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_merchants');
    }
};
