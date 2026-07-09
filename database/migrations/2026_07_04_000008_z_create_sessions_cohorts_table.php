<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sessions / Cohortes de formation.
     */
    public function up(): void
    {
        Schema::create('sessions_cohorts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('enrollment_deadline')->nullable();
            $table->integer('max_students')->nullable();
            $table->integer('enrolled_count')->default(0);
            $table->enum('status', ['upcoming', 'active', 'completed', 'cancelled'])->default('upcoming');
            $table->timestamps();

            $table->index(['course_id', 'status']);
            $table->index('start_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions_cohorts');
    }
};
