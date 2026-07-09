<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Paiements (CinetPay).
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique();
            $table->string('cinetpay_transaction_id')->nullable();
            $table->string('payment_token')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 5)->default('XOF');
            $table->enum('method', ['mobile_money', 'credit_card', 'wallet', 'bank_transfer', 'free'])->default('mobile_money');
            $table->string('channel')->nullable(); // ORANGE_CI, MTN_CI, MOOV_CI, WAVE_CI, VISA, MASTERCARD
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->string('cinetpay_status')->nullable();
            $table->json('cinetpay_response')->nullable();
            $table->string('payer_phone', 20)->nullable();
            $table->string('payer_name')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->boolean('is_webhook_verified')->default(false);
            $table->string('idempotency_key')->nullable()->unique();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('transaction_id');
            $table->index('cinetpay_transaction_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
