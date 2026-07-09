<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('forum_threads')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('forum_posts')->onDelete('cascade');
            $table->longText('body');
            $table->boolean('is_best_answer')->default(false);
            $table->integer('likes_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['thread_id']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_posts');
    }
};
