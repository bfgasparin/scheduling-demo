<?php

namespace Tests\Unit\Notifications\User;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Str;
use App\Notifications\User\ActivationToken;

/**
 * Test Activation Token notification
 */
class ActivationTokenTest extends TestCase
{
    /** @test */
    public function the_user_is_nofified_by_sms() : void
    {
        tap(new ActivationToken($token = Str::random()), function ($notification) use ($token) {
            $this->assertEquals($token, $notification->toNexmo(factory(User::class)->make())->content);
        });
    }
}
