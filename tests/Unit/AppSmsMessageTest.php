<?php

namespace Tests\Unit;

use Mockery;
use Carbon\Carbon;
use App\SmsMessage;
use Tests\TestCase;
use Ramsey\Uuid\Uuid;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Nexmo\Message\MessageInterface as NexmoMessageInterface;

/**
 * Test SmsMessage instance
 */
class AppSmsMessageTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_sms_message_can_be_marked_as_delivered() : void
    {
        $smsMessage = factory(SmsMessage::class)->states('nexmo')->create();

        tap($smsMessage, function ($smsMessage) {
            $this->assertNull($smsMessage->delivered_at);
            $this->assertTrue($smsMessage->isUndelivered());
            $this->assertFalse($smsMessage->isDelivered());
        });

        tap($smsMessage->markAsDelivered(), function ($smsMessage) {
            $this->assertTrue($smsMessage->isDelivered());
            $this->assertFalse($smsMessage->isUnDelivered());
            $this->assertEquals(Carbon::now(), $smsMessage->delivered_at);
        });
    }

    /** @test */
    public function create_an_instance_of_sms_message_from_a_nexmo_response() : void
    {
        $nexmoMessage = Mockery::mock(NexmoMessageInterface::class)
            ->shouldReceive('getMessageId')->andReturn(30)
            ->shouldReceive('getResponseData')->andReturn(['foo' => 'bar'])
            ->getMock();

        tap(SmsMessage::instanceFromNexmoResponse($nexmoMessage), function ($smsMessage) {
            $this->assertTrue(UUid::isValid($smsMessage->id));
            $this->assertEquals('nexmo', $smsMessage->channel);
            $this->assertEquals(30, $smsMessage->channel_id);
            $this->assertEquals(['foo' => 'bar'], $smsMessage->channel_response);
        });
    }

}
