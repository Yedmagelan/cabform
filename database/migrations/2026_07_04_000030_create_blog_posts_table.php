<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('tags')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->integer('views_count')->default(0);
            $table->boolean('allow_comments')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('published_at');
            if (DB::getDriverName() !== 'sqlite') {
                $table->fulltext(['title', 'content']);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
