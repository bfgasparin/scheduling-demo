<?php

namespace App\Listeners\SmsMessage;

use App\SmsMessage;
use Illuminate\Notifications\Events\NotificationSent;

/**
 * Create a SmsMessage instance from a notification that was sent through the system.
 *
 * If the notification was sent through a sms like channel, CreateFromNotification
 * creates an SmsMessage representatin of the message channel for  easy future history
 * like sms delivery reports.
 * @see App\SmsMessage
 */
class CreateFromNotification
{
    /**
     * Handle the event.
     *
     * @param  Illuminate\Notifications\Events\NotificationSent  $event
     * @return void
     */
    public function handle(NotificationSent $event)
    {
        if ($event->channel === 'nexmo') {
            $event->notifiable->attachSmsMessage(
                SmsMessage::instanceFromNexmoResponse($event->response)
            );
        }
    }
}
