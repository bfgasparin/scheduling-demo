<?php

namespace Tests\Unit\Jobs\User;

use Hash;
use Cache;
use App\User;
use Exception;
use Notification;
use Tests\TestCase;
use App\Jobs\User\SendActivationToken;
use App\Notifications\User\ActivationToken;
use App\Exceptions\Activation\ModelAlreadyActive;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Tests of 'Sending User Activation Token' Use Case
 */
class SendActivationTokenTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function send_the_token_to_user()
    {
        Notification::fake();

        $user = factory(User::class)->states('inactive')->create();

        tap(new SendActivationToken($user))->handle();

        Notification::assertSentTo(
            $user,
            ActivationToken::class,
            function ($notification, $channels) use ($user) {
                // assert it put the token encrypted to the Cache
                // and assert it sends the token to nexmo channel
                return Hash::check(
                    $notification->token, Cache::get("user_activation_token[{$user->id}]"))
                    && in_array('nexmo', $channels);
            }
        );
    }

    /** @test */
    public function fail_sending_the_token_to_user_when_user_is_already_active()
    {
        Notification::fake();

        $user = factory(User::class)->states('active')->create();

        $this->catchException(ModelAlreadyActive::class, function () use ($user) {
            tap(new SendActivationToken($user))->handle();
        });

        Notification::assertNotSentTo($user, ActivationToken::class);
    }

    /** @test */
    public function cache_is_cleared_when_sending_token_notification_fails()
    {
        $user = factory(User::class)->create();

        Cache::shouldReceive('forget')->with("user_activation_token[{$user->id}]");

        tap(new SendActivationToken($user))->failed();
    }
}

