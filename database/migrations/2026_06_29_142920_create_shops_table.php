
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('whatsapp_phone', 20);
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->json('delivery_zones')->nullable(); // ["Dakar", "Yoff", "Almadies"]
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('min_order', 10, 2)->default(0);
            $table->string('currency', 3)->default('XOF');
            $table->json('opening_hours')->nullable();
            $table->boolean('is_open')->default(true);
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
