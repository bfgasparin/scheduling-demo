<?php

namespace App\Notifications\Salon\Client\Booking;

use App\Salon\Client;
use App\Salon\Client\Booking;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * A notification to be sent when a booking is canceled.
 *
 * According to the notifiable (client or salon) a different channel and message is used to
 * send the notification
 */
class Canceled extends Notification implements ShouldQueue
{
    /** @var string */
    public $queue = 'notifications';

    /** @var int */
    public $tries = 5;

    /** @var App\Salon\Client\Booking */
    public $booking;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (is_a($notifiable, Client::class)) {
            return $this->mailToClient();
        }

        return $this->mailToSalon();
    }

    /**
     * Gets the mail representation of the notification to be sent to the client
     *
     * @return Illuminate\Notifications\Messages\MailMessage
     */
    protected function mailToClient() : MailMessage
    {
        return (new MailMessage)
            ->subject(__("Your booking nº :booking was canceled", ['booking' => $this->booking->id]))
            ->markdown('mail.salon.client.booking.canceled', ['booking' => $this->booking]);
    }

    /**
     * Gets the mail representation of the notification to be sent to the salon
     *
     * @return Illuminate\Notifications\Messages\MailMessage
     */
    protected function mailToSalon() : MailMessage
    {
        return (new MailMessage)
            ->subject(__('A booking was canceled'))
            ->markdown('mail.salon.booking.canceled', ['booking' => $this->booking]);
    }
}
