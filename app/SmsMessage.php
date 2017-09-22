<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SmsMessage\Concerns\RepresentsNexmoMessage;

/**
 * An representation of sms messages sent through the system.
 *
 * SmsMessage contains some important informations like:
 *   - the isntance in the system where the sms was sent to
 *   - the channel used to sent the sms message
 *   - the channel response when sent the sms message
 *   - the delivery_receipt to explain the delivery status of the message
 *
 * @see Model
 */
class SmsMessage extends Model
{
    use RepresentsNexmoMessage;

    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'channel_response' => 'json',
        'delivery_receipt' => 'json',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the recipient of the message to where the sms was sent to
     */
    public function recipient()
    {
        return $this->morphTo();
    }

    /**
     * Mark the SmsMessage as delivered.
     *
     * @return self
     */
    public function markAsDelivered() : self
    {
        if (! $this->delivered_at) {
            return tap($this->forceFill(['delivered_at' => $this->freshTimestamp()]))->save();
        }

        return $this;
    }

    /**
     * Returns if a sms message is delivered
     *
     * @return bool
     */
    public function isDelivered()
    {
        return $this->delivered_at !== null;
    }

    /**
     * Returns if a notification has not been delivered.
     *
     * @return bool
     */
    public function isUndelivered()
    {
        return $this->delivered_at === null;
    }
}
