<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Forum - Sujets de discussion.
     */
    public function up(): void
    {
        Schema::create('forum_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('body');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_resolved')->default(false);
            $table->integer('replies_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->timestamp('last_reply_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['course_id']);
            $table->index(['user_id']);
            $table->index('is_pinned');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_threads');
    }
};
