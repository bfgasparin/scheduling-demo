<?php

namespace Tests\Support\Fakes;

use App\SmsMessage;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert as PHPUnit;

/**
 * A Fake notification notifiable class that receives SmsMessage instances
 * @see App\SmsMessage\ReceivesSmsMessages
 */
class SmsMessageRecipientFake
{
    /**
     * The SmsMessage that have attached to this recipient
     *
     * @var array
     */
    protected $smsMessages = [];

    /**
     * Attach a SmsMessage instance to this recipient.
     *
     * @param  App\SmsMessage $smsMessage
     * @return App\SmsMessage|false
     */
    function attachSmsMessage(SmsMessage $smsMessage)
    {
        $this->smsMessages[] = $smsMessage;

        return $smsMessage;
    }

    /**
     * Assert if a SmsMessage was attached based on a truth-test callback.
     *
     * @param  string  $smsMessage
     * @param  callable|null  $callback
     * @return void
     */
    function assertSmsMessageWasAttached($callback = null) : void
    {
        PHPUnit::assertTrue(
            $this->smsMessagesAttached($callback)->count() > 0,
            "The expected [SmsMessage] was not attached to this notifiable instance."
        );
    }

    /**
     * Assert if a SmsMessage was attached based on a truth-test callback.
     *
     * @param  callable|null  $callback
     * @return void
     */
    public function assertSmsMessageWasNotAttached($callback = null) : void
    {
        PHPUnit::assertTrue(
            $this->smsMessagesAttached($callback)->count() === 0,
            "The unexpected [SmsMessage] was not attached to this notifiable instance."
        );
    }

    /**
     * Get all of the SmsMessages matching a truth-test callback.
     *
     * @param  string  $smsMessage
     * @param  callable|null  $callback
     * @return \Illuminate\Support\Collection
     */
    public function smsMessagesAttached($callback = null) : Collection
    {
        if (! $this->hasSmsMessageAttached()) {
            return collect();
        }

        $callback = $callback ?: function () {
            return true;
        };

        return collect($this->smsMessages)->filter(function ($smsMessage) use ($callback) {
            return $callback($smsMessage);
        });
    }

    /**
     * Determine if there are any stored smsMessage for a given class.
     *
     * @return bool
     */
    public function hasSmsMessageAttached() : bool
    {
        return ! empty($this->smsMessages);
    }
}
