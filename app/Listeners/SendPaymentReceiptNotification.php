<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Notifications\OrderConfirmedNotification;

class SendPaymentReceiptNotification
{
    /**
     * Handle the event.
     */
    public function handle(PaymentReceived $event): void
    {
        $payment = $event->payment;
        $user = $payment->user;
        $user->notify(new OrderConfirmedNotification($payment));
    }
}
