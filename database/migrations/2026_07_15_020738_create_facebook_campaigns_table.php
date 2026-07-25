<?php
// database/migrations/xxxx_create_facebook_campaigns_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('facebook_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            // IDs Facebook
            $table->string('fb_campaign_id')->nullable();
            $table->string('fb_adset_id')->nullable();
            $table->string('fb_ad_id')->nullable();

            // Détails de la campagne
            $table->string('name');
            $table->decimal('daily_budget', 10, 2); // en euros
            $table->decimal('total_budget', 10, 2)->nullable();
            $table->integer('duration_days');

            // Ciblage
            $table->json('targeting')->nullable();
            $table->string('audience_type')->default('local'); // local, followers, custom

            // Statut
            $table->string('status')->default('draft'); // draft, pending, active, paused, completed, rejected
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            // Statistiques
            $table->integer('reach')->default(0);
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->decimal('spent', 10, 2)->default(0);
            $table->json('stats')->nullable();

            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('facebook_campaigns');
    }
};
