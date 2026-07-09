<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Devoirs / Assignments.
     */
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('module_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('lesson_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->longText('description');
            $table->longText('instructions')->nullable();
            $table->decimal('max_score', 5, 2)->default(100);
            $table->decimal('passing_score', 5, 2)->default(50);
            $table->timestamp('due_date')->nullable();
            $table->integer('max_file_size_mb')->default(10);
            $table->json('allowed_file_types')->nullable();
            $table->integer('max_submissions')->default(1);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
