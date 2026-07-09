<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Inscriptions des apprenants aux formations.
     */
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('session_cohort_id')->nullable()->constrained('sessions_cohorts')->onDelete('set null');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('partner_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['pending', 'active', 'completed', 'suspended', 'cancelled', 'expired'])->default('pending');
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->integer('last_lesson_id')->nullable();
            $table->integer('time_spent_minutes')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
            $table->index(['status']);
            $table->index(['user_id', 'status']);
            $table->index(['course_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
