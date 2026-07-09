<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tentatives de quiz par les apprenants.
     */
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->integer('attempt_number')->default(1);
            $table->decimal('score', 5, 2)->nullable();
            $table->decimal('max_score', 5, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->boolean('passed')->nullable();
            $table->json('answers_data')->nullable();
            $table->enum('status', ['in_progress', 'submitted', 'graded', 'abandoned'])->default('in_progress');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('feedback')->nullable();
            $table->integer('time_spent_seconds')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'quiz_id']);
            $table->index(['quiz_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
