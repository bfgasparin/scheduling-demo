<?php

namespace Tests\Integration\Jobs\User;

use Hash;
use Cache;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use App\Jobs\User\SendActivationToken;
use Tests\Concerns\InteractsWithNexmo;

/**
 * Integration Tests of 'Sending User Activation Token' Use Case
 */
class SendActivationTokenTest extends TestCase
{
    use InteractsWithNexmo;

    /**
     * @test
     * @group external-interaction
     */
    public function send_the_token_to_nexmo_channel_and_receive_a_callback_from_it()
    {
        $user = User::withInactive()->firstOrCreate(
            ['cellphone' => config('sms.to')],
            factory(User::class)->make()->makeVisible('password')->toArray()
        );

        $messages = $this->nexmoMessagesDuring(function () use ($user) {
            tap(new SendActivationToken($user))->handle();
        });

        tap($messages, function ($messages) use ($user) {
            $this->assertCount(1, $messages);
            $this->assertTrue($messages->first()->valid());
            $this->assertEquals('55'.config('sms.to'), $messages->first()->getTo());
            $this->assertTrue(Hash::check($messages->first()['text'], Cache::get("user_activation_token[{$user->id}]")));

            // For status code definition of the nexmo message,
            // see https://docs.nexmo.com/messaging/sms-api/api-reference#response
            $this->assertEquals('0', $messages->first()->getStatus());

            $this->assertDatabaseHas('sms_messages', [
                'recipient_id' => $user->id,
                'recipient_type' => User::class,
                'channel' => 'nexmo',
                'channel_id' => $messages->first()->getMessageId(),
            ]);
        });

        $requestInput = $this->waitForNexmoCallbackRouteBeTouched(function ($requestInput) use ($messages) {
            return $requestInput['messageId'] === $messages->first()->getMessageId();
        });

        $this->assertDatabaseHas('sms_messages', [
            'recipient_id' => $user->id,
            'recipient_type' => User::class,
            'channel' => 'nexmo',
            'channel_id' => $messages->first()->getMessageId(),
            'delivered_at' => Carbon::createFromFormat('ymdHi', $requestInput['scts']),
        ]);
    }
}

