
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();

            // Client info
            $table->string('customer_name');
            $table->string('customer_phone', 20);
            $table->string('customer_email')->nullable();
            $table->text('customer_address')->nullable();
            $table->text('customer_note')->nullable();

            // Financial
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Payment
            $table->enum('payment_method', ['cash_on_delivery', 'wave', 'orange_money', 'card', 'other'])->default('cash_on_delivery');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_transaction_id')->nullable();
            $table->json('payment_metadata')->nullable();

            // Status
            $table->enum('order_status', [
                'pending', 'confirmed', 'preparing', 'ready',
                'out_for_delivery', 'delivered', 'cancelled', 'rejected'
            ])->default('pending');

            // WhatsApp notification
            $table->boolean('whatsapp_notification_sent')->default(false);
            $table->timestamp('whatsapp_notified_at')->nullable();

            // Tracking
            $table->string('delivery_person')->nullable();
            $table->string('delivery_person_phone')->nullable();
            $table->text('status_history')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
