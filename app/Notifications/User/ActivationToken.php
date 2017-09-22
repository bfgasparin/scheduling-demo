<?php

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\NexmoMessage;

/**
 * A user notification containing a token to active its account.
 *
 * @see Notification
 */
class ActivationToken extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var int */
    public $tries = 5;

    /** @var string */
    public $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $token)
    {
        $this->token = $token;

        $this->onQueue('notifications');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ['nexmo'];
    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @return NexmoMessage
     */
    public function toNexmo()
    {
        return (new NexmoMessage)
            ->content($this->token);
    }
}
