<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Progression des apprenants par leçon.
     */
    public function up(): void
    {
        Schema::create('progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
            $table->integer('time_spent_seconds')->default(0);
            $table->integer('video_position_seconds')->nullable();
            $table->decimal('completion_percentage', 5, 2)->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'lesson_id']);
            $table->index(['enrollment_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress');
    }
};
