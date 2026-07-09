<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Coupons / Codes promo.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 10, 2);
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->decimal('max_discount_amount', 10, 2)->nullable();
            $table->integer('max_uses')->nullable();
            $table->integer('max_uses_per_user')->default(1);
            $table->integer('used_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('applicable_courses')->nullable();
            $table->json('applicable_categories')->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
