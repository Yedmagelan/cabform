<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Partenaires / Entreprises (B2B).
     */
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('registration_number')->nullable();
            $table->string('tax_id')->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->default('Côte d\'Ivoire');
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->integer('max_learners')->default(50);
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('company_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
