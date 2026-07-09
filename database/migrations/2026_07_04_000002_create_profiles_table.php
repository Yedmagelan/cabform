<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Profils utilisateurs étendus.
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('nationality', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->default('Côte d\'Ivoire');
            $table->string('postal_code', 20)->nullable();
            $table->string('company')->nullable();
            $table->string('job_title')->nullable();
            $table->text('bio')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('website_url')->nullable();
            $table->enum('education_level', ['bac', 'bac+2', 'bac+3', 'bac+5', 'doctorat', 'autre'])->nullable();
            $table->json('interests')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
