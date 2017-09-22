<?php

namespace Tests\Feature\API\ForNexmoAPI;

use Carbon\Carbon;
use App\SmsMessage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Testing NEXMO API sending Delivery Recipient (DLR) to the callback route
 */
class SendDeliveryRecipientTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function mark_sms_message_as_delivered() : void
    {
        $smsMessage = factory(SmsMessage::class)->states('nexmo')->create([
            'channel_id' => with($id = str_random(10)),
        ]);

        $this->json('POST', 'api/nexmo/callback', [
            'foo' => 'bar',
            'scts' => with($scts = Carbon::now()->format('ymdHi')),
            'messageId' => $id,
        ])->assertSuccessful();

        $this->assertDatabaseHas(
            'sms_messages',
            array_merge($smsMessage->makeHidden('channel_response')->toArray(), [
                'delivered_at' => Carbon::createFromFormat('ymdHi', $scts),
            ])
        );

        tap($smsMessage->fresh(), function ($smsMessage) use ($scts, $id) {
            $this->assertEquals(Carbon::createFromFormat('ymdHi', $scts), $smsMessage->delivered_at);
            $this->assertEquals(['foo' => 'bar', 'scts' => $scts, 'messageId' => $id], $smsMessage->delivery_receipt);
        });
    }
}
