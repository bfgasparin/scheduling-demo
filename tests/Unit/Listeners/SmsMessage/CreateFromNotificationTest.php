<?php

namespace Tests\Unit\Listeners\SmsMessage;

use Mockery;
use Tests\TestCase;
use Ramsey\Uuid\Uuid;
use Tests\Support\Fakes\SmsMessageRecipientFake;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Nexmo\Message\MessageInterface as NexmoMessageInterface;
use Illuminate\Notifications\Events\NotificationSent;
use App\Listeners\SmsMessage\CreateFromNotification as CreateSmsMessageFromNotification;

/**
 * Tests of CreateFromNotification Listener
 */
class CreateFromNotificationTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function attach_a_sms_message_to_notifiable_when_notification_was_sent_via_nexmo_channel() : void
    {
        // @var Illuminate\Notifications\Events\NotificationSent
        $event = tap($this->notificationEvent('nexmo'), function ($event) {
            $event->response
                ->shouldReceive('getMessageId')->andReturn('3243')
                ->shouldReceive('getResponseData')->andReturn(['foo' => 'bar']);
        });

        tap(new CreateSmsMessageFromNotification)->handle($event);

        $event->notifiable->assertSmsMessageWasAttached(function ($smsMessage) {
            return UUid::isValid($smsMessage->id)
                && $smsMessage->channel === 'nexmo'
                && $smsMessage->channel_id === '3243'
                && $smsMessage->channel_response === ['foo' => 'bar'];
        });
    }

    /**
     * @test
     * @dataProvider invalidChannels
     */
    public function do_nothing_to_notifiable_when_notification_was_not_send_via_nexmo_channel($channel) : void
    {
        /** @var Illuminate\Notifications\Events\NotificationSent */
        $event = $this->notificationEvent($channel);

        tap(new CreateSmsMessageFromNotification)->handle($event);

        $event->notifiable->assertSmsMessageWasNotAttached();
    }

    /**
     * Get an instance of NotificationSent event with faked data
     *
     * @param string $channel
     */
    public function notificationEvent(string $channel) : NotificationSent
    {
        return new NotificationSent(
            $this->notifiable(),
            'nexmo',
            $channel,
            Mockery::mock(NexmoMessageInterface::class)
        );
    }

    /**
     * Return a notifiable instance
     *
     * @return anonymous class
     */
    public function notifiable()
    {
        return new SmsMessageRecipientFake();
    }

    /**
     * Returns a list of notifications channels except nexmo channel
     *
     * @return array
     */
    public function invalidChannels()
    {
        return [
            ['mail'],
            ['broadcast'],
            ['database'],
            ['broadcast'],
            ['slack'],
            [str_random()],
        ];
    }
}
