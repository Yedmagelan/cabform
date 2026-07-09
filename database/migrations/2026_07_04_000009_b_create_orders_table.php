<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Commandes (panier validé).
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('partner_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('set null');
            $table->string('order_number')->unique();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('currency', 5)->default('XOF');
            $table->enum('status', ['pending', 'paid', 'failed', 'cancelled', 'refunded', 'partially_refunded'])->default('pending');
            $table->text('notes')->nullable();
            $table->json('billing_info')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index('order_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
