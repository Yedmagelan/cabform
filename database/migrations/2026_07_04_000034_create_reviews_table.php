<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Avis / Reviews des formations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
            $table->index(['course_id', 'is_approved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
