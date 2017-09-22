<?php

namespace Tests\Unit\Jobs\User;

use Cache;
use App\User;
use Tests\TestCase;
use App\Jobs\User\Activate as UserActivate;
use App\Exceptions\Activation\InvalidActivationToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Tests of 'User Activation' Use Case
 */
class ActivateTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function active_the_user_successfully() : void
    {
        $user = factory(User::class)->states('inactive')->create();

        Cache::shouldReceive('get')
            ->with("user_activation_token[{$user->id}]")
            ->andReturn(bcrypt(with($token = str_random(4))));

        tap((new UserActivate($user, $token))->handle(), function ($user) {
            $this->assertTrue($user->isActive());

            $this->assertDatabaseHas('users',
                ['active' => true] + $user->toArray()
            );
        });
    }

    /**
     * @test
     * @expectedException App\Exceptions\Activation\ModelAlreadyActive
     */
    public function fails_when_the_user_is_already_active() : void
    {
        $user = factory(User::class)->states('active')->create();

        Cache::shouldReceive('get')
            ->with("user_activation_token[{$user->id}]")
            ->andReturn(bcrypt(with($token = str_random(4))));

        tap(new UserActivate($user, $token))->handle();
    }

    /** @test */
    public function fails_when_the_token_expired() : void
    {
        $user = factory(User::class)->states('inactive')->create();

        Cache::shouldReceive('get')
            ->with("user_activation_token[{$user->id}]")
            ->andReturn(null);

        $this->catchException(InvalidActivationToken::class, function () use ($user) {
            tap(new UserActivate($user, str_random(4)))->handle();
        });

        $this->assertFalse($user->isActive());
        $this->assertDatabaseHas('users',
            ['active' => false] + $user->toArray()
        );
    }

    /** @test */
    public function fails_when_the_token_into_cache_could_not_be_checked() : void
    {
        $user = factory(User::class)->states('inactive')->create();

        Cache::shouldReceive('get')
            ->with("user_activation_token[{$user->id}]")
            ->andReturn(str_random(4));

        $this->catchException(InvalidActivationToken::class, function () use ($user) {
            tap(new UserActivate($user, str_random(4)))->handle();
        });

        $this->assertFalse($user->isActive());
        $this->assertDatabaseHas('users',
            ['active' => false] + $user->toArray()
        );
    }
}
