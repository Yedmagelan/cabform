<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Questions des quiz.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->text('explanation')->nullable();
            $table->enum('type', ['mcq', 'true_false', 'short_answer', 'matching', 'open_ended', 'fill_blank'])->default('mcq');
            $table->string('image')->nullable();
            $table->decimal('points', 5, 2)->default(1);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['quiz_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
