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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'entry', 'return', 'loss'
            $table->integer('quantity'); // Toujours positif
            $table->string('reason')->nullable();
            $table->decimal('new_cost_price', 15, 2)->nullable(); // Nouveau prix d'achat (optionnel)
            $table->decimal('new_sale_price', 15, 2)->nullable(); // Nouveau prix de vente (optionnel)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
