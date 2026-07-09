<?php

namespace App\Jobs;

use App\Models\Certificate;
use App\Services\CertificateService;
use App\Notifications\CertificateGeneratedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateCertificatePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Certificate $certificate) {}

    /**
     * Execute the job.
     */
    public function handle(CertificateService $service): void
    {
        // 1. Générer le PDF
        $service->generatePdf($this->certificate);

        // 2. Envoyer la notification de certificat généré à l'utilisateur
        $user = $this->certificate->user;
        $user->notify(new CertificateGeneratedNotification($this->certificate));
    }
}
