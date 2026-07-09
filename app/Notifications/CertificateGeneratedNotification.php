<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateGeneratedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Certificate $certificate) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject('Félicitations pour votre certification ! - CabForm')
            ->greeting("Félicitations {$notifiable->first_name} !,")
            ->line("Vous avez validé avec succès la formation : {$this->certificate->course->title}.")
            ->line("Votre certificat de réussite a été généré avec le numéro unique : {$this->certificate->certificate_number}.")
            ->line("Vous pouvez le télécharger ou le partager dès maintenant.");

        if ($this->certificate->pdf_path) {
            $mailMessage->attach(storage_path('app/public/' . $this->certificate->pdf_path), [
                'as' => 'Certificat-' . $this->certificate->certificate_number . '.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        $mailMessage->action('Voir mon certificat', route('learner.certificates'))
            ->line('Merci d\'avoir choisi CabForm pour votre parcours professionnel !');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'certificate_id' => $this->certificate->id,
            'certificate_number' => $this->certificate->certificate_number,
            'course_title' => $this->certificate->course->title,
            'message' => "Félicitations ! Votre certificat pour la formation {$this->certificate->course->title} a été généré.",
        ];
    }
}
