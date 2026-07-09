<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Soumissions de devoirs.
     */
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->longText('content')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->boolean('passed')->nullable();
            $table->longText('feedback')->nullable();
            $table->enum('status', ['submitted', 'under_review', 'graded', 'returned'])->default('submitted');
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();

            $table->index(['assignment_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
