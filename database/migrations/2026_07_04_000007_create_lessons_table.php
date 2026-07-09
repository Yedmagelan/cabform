<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Leçons de modules.
     */
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->enum('type', ['video', 'text', 'pdf', 'audio', 'slide', 'embed', 'scorm'])->default('text');
            $table->string('video_url')->nullable();
            $table->string('video_provider')->nullable(); // youtube, vimeo, upload
            $table->integer('duration_minutes')->default(0);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_free_preview')->default(false);
            $table->boolean('is_downloadable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('meta_data')->nullable();
            $table->timestamps();

            $table->index(['module_id', 'sort_order']);
            $table->unique(['module_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
