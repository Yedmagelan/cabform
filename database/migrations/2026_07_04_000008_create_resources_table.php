<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ressources pédagogiques attachées aux leçons.
     */
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type', 50);
            $table->unsignedBigInteger('file_size')->default(0); // en bytes
            $table->enum('type', ['document', 'video', 'audio', 'image', 'archive', 'link'])->default('document');
            $table->boolean('is_downloadable')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('lesson_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
