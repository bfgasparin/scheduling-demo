<?php

namespace Tests\Unit\Jobs\User\Registration;

use Bus;
use Auth;
use App\User;
use Tests\TestCase;
use App\Auth\UserProvider;
use Tymon\JWTAuth\Token as JWTToken;
use App\Jobs\User\SendActivationToken;
use App\Jobs\Auth\User\Login as UserLogin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Contracts\Auth\Factory as AuthFactoryContract;
use App\Exceptions\Auth\User\{InvalidCredentials, IsNotActive};

/**
 * Tests of 'User Login' Use Case
 */
class LoginTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function login_to_the_system_using_email_as_login()
    {
        $user = factory(User::class)->states('active')->create([
            'password' => with($password = str_random(6)),
        ]);

        tap((new UserLogin($user->email, $password))->handle(
            app(AuthFactoryContract::class),
            app(UserProvider::class)),
        function ($token) use ($user) {
            // validates if it is a valid jwt token
            tap(new JWTToken($token), function ($token) use ($user) {
                $payload = Auth::guard('api-users')->manager()->decode($token);

                $this->assertEquals($user->id, $payload->get('sub'));
                $this->assertEquals('App\User', $payload->get('typ'));
            });
        });

        $this->assertTrue(Auth::guard('api-users')->user()->is($user));
    }

    /** @test */
    public function login_to_the_system_using_cellphone_as_login()
    {
        $user = factory(User::class)->states('active')->create([
            'password' => with($password = str_random(6)),
        ]);

        tap((new UserLogin($user->email, $password))->handle(
            app(AuthFactoryContract::class),
            app(UserProvider::class)),
        function ($token) use ($user) {
            // validates if it is a valid jwt token
            tap(new JWTToken($token), function ($token) use ($user) {
                $payload = Auth::guard('api-users')->manager()->decode($token);

                $this->assertEquals($user->id, $payload->get('sub'));
                $this->assertEquals('App\User', $payload->get('typ'));
            });
        });

        $this->assertTrue(Auth::guard('api-users')->user()->is($user));
    }

    /** @test */
    public function fails_when_credentials_are_not_valid() : void
    {
        // there is some user in database
        factory(User::class)->states('active')->create();

        $e = $this->catchException(InvalidCredentials::class, function () {
            tap(new UserLogin(str_random(6), str_random(6)))->handle(
                app(AuthFactoryContract::class), app(UserProvider::class)
            );
        });

        $this->assertArraySubset(['WWW-Authenticate' => 'jwt-auth'], $e->getHeaders());
    }

    /** @test */
    public function fails_with_email_when_password_does_not_match_with_database() : void
    {
        $user = factory(User::class)->states('active')->create();

        $e = $this->catchException(InvalidCredentials::class, function () use ($user) {
            tap(new UserLogin($user->email, str_random(6)))->handle(
                app(AuthFactoryContract::class), app(UserProvider::class)
            );
        });

        $this->assertArraySubset(['WWW-Authenticate' => 'jwt-auth'], $e->getHeaders());
    }

    /** @test */
    public function fails_with_cellphone_when_password_does_not_match_with_database() : void
    {
        $user = factory(User::class)->states('active')->create();

        $e = $this->catchException(InvalidCredentials::class, function () use ($user) {
            tap(new UserLogin($user->cellphone, str_random(6)))->handle(
                app(AuthFactoryContract::class), app(UserProvider::class)
            );
        });

        $this->assertArraySubset(['WWW-Authenticate' => 'jwt-auth'], $e->getHeaders());
    }

    /** @test */
    public function fails_with_email_and_inactive_account_when_password_does_not_match_with_database() : void
    {
        $user = factory(User::class)->states('inactive')->create();

        $e = $this->catchException(InvalidCredentials::class, function () use ($user) {
            tap(new UserLogin($user->email, str_random(6)))->handle(
                app(AuthFactoryContract::class), app(UserProvider::class)
            );
        });

        $this->assertArraySubset(['WWW-Authenticate' => 'jwt-auth'], $e->getHeaders());
    }

    /** @test */
    public function fails_with_cellphone_and_inactive_when_password_does_not_match_with_database() : void
    {
        $user = factory(User::class)->states('inactive')->create();

        $e = $this->catchException(InvalidCredentials::class, function () use ($user) {
            tap(new UserLogin($user->cellphone, str_random(6)))->handle(
                app(AuthFactoryContract::class), app(UserProvider::class)
            );
        });

        $this->assertArraySubset(['WWW-Authenticate' => 'jwt-auth'], $e->getHeaders());
    }

    /** @test */
    public function sends_a_token_activation_when_when_logging_with_email() : void
    {
        Bus::fake();

        $user = factory(User::class)->states('inactive')->create([
            'password' => with($password = str_random(6)),
        ]);

        $this->catchException(IsNotActive::class, function () use ($user, $password) {
            tap(new UserLogin($user->email, $password))->handle(
                app(AuthFactoryContract::class),
                app(UserProvider::class)
            );
        });

        Bus::assertDispatched(SendActivationToken::class, function ($job) use ($user) {
            return $job->user->is($user);
        });
    }

    /** @test */
    public function sends_a_token_activation_when_logging_with_cellphone() : void
    {
        Bus::fake();

        $user = factory(User::class)->states('inactive')->create([
            'password' => with($password = str_random(6)),
        ]);

        $this->catchException(IsNotActive::class, function () use ($user, $password) {
            tap(new UserLogin($user->cellphone, $password))->handle(
                app(AuthFactoryContract::class),
                app(UserProvider::class)
            );
        });

        Bus::assertDispatched(SendActivationToken::class, function ($job) use ($user) {
            return $job->user->is($user);
        });
    }

    /** @test */
    public function job_should_not_be_queued()
    {
        $this->assertFalse(is_a(new UserLogin(str_random(6), str_random(6)), ShouldQueue::class));
    }
}
