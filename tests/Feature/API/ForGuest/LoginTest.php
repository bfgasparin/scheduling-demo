<?php

namespace Tests\Feature\API\ForGuest;

use Bus;
use App\User;
use Tests\TestCase;
use App\Jobs\Auth\User\Login as UserLogin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Exceptions\Auth\User\{InvalidCredentials, IsNotActive as UserIsNotActive};

/**
 * Testing a user logging into the system
 */
class LoginTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function login_field_is_required() : void
    {
        $this->json('POST', 'api/users/login', [
            'password' => str_random(6)
        ])->assertStatus(422)
        ->assertJsonFragment(['login']);
    }

    /** @test */
    public function password_field_is_required() : void
    {
        $this->json('POST', 'api/users/login', [
            'login' => str_random(6)
        ])->assertStatus(422)
        ->assertJsonFragment(['password']);
    }

    /** @test */
    public function login_field_must_be_a_string() : void
    {
        $this->json('POST', 'api/users/login', [
            'login' => rand(1,100)
        ])->assertStatus(422)
        ->assertJsonFragment(['login']);
    }

    /** @test */
    public function password_field_must_be_a_string() : void
    {
        $this->json('POST', 'api/users/login', [
            'password' => rand(1,100)
        ])->assertStatus(422)
        ->assertJsonFragment(['password']);
    }

    /** @test */
    public function a_user_logins_to_the_system_and_receives_the_login_token() : void
    {
        Bus::shouldReceive('dispatch')->once()
            ->with(anInstanceOf(UserLogin::class))
            ->andReturn($token = str_random(10));

        $this->json('POST', 'api/users/login', [
            'login' => str_random(6),
            'password' => str_random(6),
        ])->assertSuccessful()
        ->assertJson(['token' => $token]);
    }

    /** @test */
    public function a_user_can_not_login_to_the_system_when_credentials_are_not_valid() : void
    {
        Bus::shouldReceive('dispatch')->once()
            ->with(anInstanceOf(UserLogin::class))
            ->andThrow(new InvalidCredentials(with($challenge = 'jwt-auth')));

        $this->json('POST', 'api/users/login', [
            'login' => str_random(6),
            'password' => str_random(6),
        ])->assertStatus(401)
        ->assertHeader('WWW-Authenticate', $challenge);
    }

    /** @test */
    public function a_user_can_not_login_to_the_system_when_its_account_is_not_active() : void
    {
        Bus::shouldReceive('dispatch')->once()
            ->with(anInstanceOf(UserLogin::class))
            ->andThrow(new UserIsNotActive(new User()));

        $this->json('POST', 'api/users/login', [
            'login' => str_random(6),
            'password' => str_random(6),
        ])->assertStatus(403);
    }
}
