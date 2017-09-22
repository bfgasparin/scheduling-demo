<?php

namespace App\SmsMessage;

use App\SmsMessage;

/**
 * Help Eloquent models to be able to receive sms messages
 */
trait ReceivesSmsMessages
{
    /**
     * Get the sms messages of the model
     */
    public function smsMessages()
    {
        return $this->morphMany(SmsMessage::class, 'recipient')
                            ->orderBy('created_at', 'desc');
    }

    /**
     * Attach a SmsMessage instance to the model.
     *
     * @param  App\SmsMessage $smsMessage
     * @return App\SmsMessage|false
     */
    public function attachSmsMessage(SmsMessage $smsMessage)
    {
        return $this->smsMessages()->save($smsMessage);
    }

    /**
     * Route notifications for the Nexmo channel.
     *
     * @return string
     */
    public function routeNotificationForNexmo()
    {
        return '55'.$this->cellphone;
    }
}
