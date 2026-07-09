<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Certificats délivrés aux apprenants.
     */
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            $table->foreignId('certificate_template_id')->nullable()->constrained()->onDelete('set null');
            $table->string('certificate_number')->unique();
            $table->string('hash', 64)->unique();
            $table->string('qr_code_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('verification_url')->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->enum('status', ['pending', 'generated', 'delivered', 'revoked'])->default('pending');
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('revocation_reason')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'course_id']);
            $table->index('status');
            $table->index('certificate_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
