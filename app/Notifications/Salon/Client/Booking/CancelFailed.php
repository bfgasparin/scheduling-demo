<?php

namespace App\Notifications\Salon\Client\Booking;

use App\Salon\Client\Booking;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Salon\{Client, Client\User as ClientUser};
use Illuminate\Notifications\Messages\MailMessage;

/**
 * A notification to be sent when a booking cancel fails.
 *
 * According to the notifiable (client, client user, or salon) a different channel and message is used to
 * send the notification
 */
class CancelFailed extends Notification implements ShouldQueue
{
    /** @var string */
    public $queue = 'notifications';

    /** @var int */
    public $tries = 5;

    /** @var App\Salon\Client\Booking */
    public $booking;

    /** @var string */
    public $reason;

    /**
     * Create a new notification instance.
     *
     * @param Booking $booking The failed booking
     * @param string  $reason  The reason the booking cancel failed
     *
     * @return void
     */
    public function __construct(Booking $booking, string $reason)
    {
        $this->booking = $booking;
        $this->reason = $reason;
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
        if (is_a($notifiable, Client::class) || is_a($notifiable, ClientUser::class)) {
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
            ->subject(__("Your booking nº :booking could not be canceled", ['booking' => $this->booking->id]))
            ->markdown(
                'mail.salon.client.booking.cancel-failed',
                ['booking' => $this->booking, 'reason' => $this->reason]
            );
    }

    /**
     * Gets the mail representation of the notification to be sent to the salon
     *
     * @return Illuminate\Notifications\Messages\MailMessage
     */
    protected function mailToSalon() : MailMessage
    {
        return (new MailMessage)
            ->subject(__('A booking cancel failed'))
            ->markdown(
                'mail.salon.booking.cancel-failed',
                ['booking' => $this->booking, 'reason' => $this->reason]
            );
    }
}
