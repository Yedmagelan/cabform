<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Payment $payment) {}

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
        return (new MailMessage)
            ->subject('Confirmation de votre commande - CabForm')
            ->greeting("Bonjour {$notifiable->first_name},")
            ->line("Nous vous remercions de votre confiance. Votre paiement de {$this->payment->amount} {$this->payment->currency} a été traité avec succès.")
            ->line("Référence de la transaction : {$this->payment->transaction_id}")
            ->line("Votre inscription à la formation a été validée. Vous pouvez dès à présent démarrer votre apprentissage.")
            ->action('Accéder à mon tableau de bord', route('learner.dashboard'))
            ->line('Merci de vous former avec CabForm !');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'currency' => $this->payment->currency,
            'transaction_id' => $this->payment->transaction_id,
            'message' => "Votre paiement de {$this->payment->amount} {$this->payment->currency} a été traité avec succès.",
        ];
    }
}
