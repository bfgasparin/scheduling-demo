<?php

namespace App\Notifications\Salon\Client\Booking;

use App\Salon;
use App\Salon\Service;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Salon\{Client, Client\User as ClientUser};
use Illuminate\Notifications\Messages\MailMessage;

/**
 * A notification to be sent when a booking creation fails.
 *
 * According to the notifiable (client, client user or salon) a different channel and message is used to
 * send the notification
 */
class CreationFailed extends Notification implements ShouldQueue
{
    /** @var string */
    public $queue = 'notifications';

    /** @var int */
    public $tries = 5;

    /** @var App\Salon */
    public $salon;

    /** @var array */
    public $data;

    /** @var string */
    public $reason;

    /**
     * Create a new notification instance.
     *
     * @param App\Salon $salon
     * @param array $data
     * @param string  $reason  The reason the booking creation failed
     *
     * @return void
     */
    public function __construct(Salon $salon, array $data, string $reason)
    {
        $this->salon = $salon;
        $this->data = $data;
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
        $parameters = $this->mailParameters($this->salon, $this->data, $this->reason);

        return (new MailMessage)
            ->subject(__("Your booking could not be created"))
            ->markdown('mail.salon.client.booking.creation-failed', $parameters);
    }

    /**
     * Gets the mail representation of the notification to be sent to the salon
     *
     * @return Illuminate\Notifications\Messages\MailMessage
     */
    protected function mailToSalon() : MailMessage
    {
        $parameters = $this->mailParameters($this->salon, $this->data, $this->reason);

        return (new MailMessage)
            ->subject(__('A booking creation failed'))
            ->markdown('mail.salon.booking.creation-failed', $parameters);
    }

    /**
     * Construct and return the parameters to used to MailMessage
     *
     * @param App\Salon $salon
     * @param array $data
     * @param string $reason
     */
    protected function mailParameters(Salon $salon, array $data, string $reason) : array
    {
        $service = Service::find($data['service_id']);

        return compact('salon', 'data', 'reason', 'service');
    }
}
