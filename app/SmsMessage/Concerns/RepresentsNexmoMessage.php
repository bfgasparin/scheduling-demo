<?php

namespace App\SmsMessage\Concerns;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Builder;
use Nexmo\Message\MessageInterface as NexmoMessageInterface;

/**
 * Adds helpful methods on SmsMessage to manage Nexmo messages instances
 */
trait RepresentsNexmoMessage
{
    /**
     * Scope a query to only include models representing a nexmo message.
     *
     * If $nexmoId is setted, scope for a nexmo message with the given $nexmoId
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string $nexmoId
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeNexmoMessage(Builder $query, string $nexmoId = null) : Builder
    {
        return $query->where('channel', 'nexmo')
            ->when($nexmoId, function (Builder $query) use ($nexmoId) {
                return $query->where('channel_id', $nexmoId);
            });
    }

    /**
     * Create a new instance from a Nexmo API Response (Nexmo\Message\Message)
     *
     * @param NexmoMessage $message
     * @param mixed $notifiable
     *
     * @return void
     */
    public static function instanceFromNexmoResponse(NexmoMessageInterface $message) : self
    {
        return new static([
            'id' => Uuid::uuid4()->toString(),
            'channel' => 'nexmo',
            'channel_id' => $message->getMessageId(),
            'channel_response' => $message->getResponseData(),
        ]);
    }


    /**
     * Mark the SmsMessage as delivered from a Nexmo Delivery Recipient (DLR) extracted
     * from the Nexmo DLR callback
     * @see https://docs.nexmo.com/messaging/sms-api/api-reference#delivery_receipt
     *
     * @param array $dlr  The Nexmo DLR data
     *
     * @return self
     */
    public function markAsDeliveredFromNexmoDLR(array $dlr) : self
    {
        if (is_null($this->delivered_at)) {
            return tap($this->forceFill([
                'delivery_receipt' => $dlr,
                'delivered_at' => Carbon::createFromFormat('ymdHi', $dlr['scts']),
            ]))->save();
        };

        return $this;
    }


}
