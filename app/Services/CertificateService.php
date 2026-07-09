<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    /**
     * Générer un certificat pour un utilisateur ayant terminé une formation.
     */
    public function generate(User $user, Course $course, Enrollment $enrollment, ?float $finalScore = null): Certificate
    {
        // Vérifier si un certificat existe déjà
        $existing = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'generated')
            ->first();

        if ($existing) {
            return $existing;
        }

        $template = CertificateTemplate::where('is_default', true)->where('is_active', true)->first();
        $certificateNumber = 'CERT-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'enrollment_id' => $enrollment->id,
            'certificate_template_id' => $template?->id,
            'certificate_number' => $certificateNumber,
            'hash' => hash('sha256', $user->id . '-' . $course->id . '-' . time() . '-' . Str::random(10)),
            'final_score' => $finalScore,
            'status' => 'generated',
            'issued_at' => now(),
            'expires_at' => now()->addYears(3),
            'verification_url' => url('/verify-certificate?code=' . $certificateNumber),
        ]);

        // Déclencher la génération de PDF asynchrone via un Job
        \App\Jobs\GenerateCertificatePdfJob::dispatch($certificate);

        return $certificate;
    }

    /**
     * Générer le fichier PDF du certificat.
     */
    public function generatePdf(Certificate $certificate): string
    {
        $certificate->load(['user', 'course', 'template']);

        $data = [
            'certificate' => $certificate,
            'user' => $certificate->user,
            'course' => $certificate->course,
            'template' => $certificate->template,
            'qrCodeUrl' => $certificate->verification_url,
        ];

        $pdf = Pdf::loadView('certificates.template', $data)
            ->setPaper('a4', 'landscape')
            ->setOption('isRemoteEnabled', true);

        $filename = 'certificates/' . $certificate->certificate_number . '.pdf';

        Storage::disk('public')->put($filename, $pdf->output());

        $certificate->update(['pdf_path' => $filename]);

        return $filename;
    }

    /**
     * Vérifier un certificat par son numéro.
     */
    public function verify(string $code): ?Certificate
    {
        return Certificate::with(['user', 'course'])
            ->where('certificate_number', $code)
            ->where('status', 'generated')
            ->first();
    }

    /**
     * Révoquer un certificat.
     */
    public function revoke(Certificate $certificate, string $reason = ''): void
    {
        $certificate->update([
            'status' => 'revoked',
            'revoked_at' => now(),
            'revoke_reason' => $reason,
        ]);
    }
}
