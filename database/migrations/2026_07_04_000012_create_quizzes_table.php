<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Quiz et examens.
     */
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('module_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->enum('type', ['quiz', 'exam', 'practice', 'survey'])->default('quiz');
            $table->integer('duration_minutes')->nullable();
            $table->decimal('passing_score', 5, 2)->default(70);
            $table->integer('max_attempts')->default(3);
            $table->boolean('shuffle_questions')->default(true);
            $table->boolean('shuffle_answers')->default(true);
            $table->boolean('show_correct_answers')->default(false);
            $table->boolean('show_score_immediately')->default(true);
            $table->integer('questions_per_attempt')->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['course_id', 'type']);
            $table->index(['module_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
