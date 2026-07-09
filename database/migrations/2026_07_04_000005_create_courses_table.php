<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Formations / Cours.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->text('objectives')->nullable();
            $table->text('prerequisites')->nullable();
            $table->text('target_audience')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('preview_video')->nullable();
            $table->enum('level', ['debutant', 'intermediaire', 'avance', 'expert'])->default('debutant');
            $table->string('language', 5)->default('fr');
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->boolean('is_free')->default(false);
            $table->integer('duration_hours')->default(0);
            $table->integer('max_students')->nullable();
            $table->boolean('is_certified')->default(true);
            $table->boolean('sequential_unlock')->default(true);
            $table->boolean('is_self_paced')->default(true);
            $table->enum('status', ['draft', 'pending_review', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->integer('enrollment_count')->default(0);
            $table->integer('version')->default(1);
            $table->json('meta_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('level');
            $table->index('is_free');
            $table->index('is_certified');
            $table->index('published_at');
            $table->index('price');
            if (DB::getDriverName() !== 'sqlite') {
                $table->fulltext(['title', 'description']);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
