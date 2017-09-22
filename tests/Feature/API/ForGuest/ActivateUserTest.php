<?php

namespace Tests\Feature\API\ForGuest;

use Bus;
use Auth;
use App\User;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Jobs\User\{Activate as UserActivate, SendActivationToken};
use App\Exceptions\Activation\{InvalidActivationToken, ModelAlreadyActive};

/**
 * Testing a user activating its account
 */
class ActivateUserTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_user_requests_a_new_token_to_active_its_account()
    {
        Bus::fake();

        $user = factory(User::class)->states('inactive')->create();
        $this->json('POST', "/api/userActivation/token", with(['cellphone' => $user->cellphone]))
            ->assertSuccessful()
            ->assertSee('Token was sent');

        Bus::assertDispatched(SendActivationToken::class, function ($job) use ($user) {
            return $job->user->is($user);
        });
    }

    /** @test */
    public function validates_cellphone_required_to_send_a_token()
    {
        Bus::fake();

        $this->json('POST', "/api/userActivation/token", with([]))
            ->assertStatus(422)
            ->assertJsonFragment(['cellphone']);

        Bus::assertNotDispatched(SendActivationToken::class);
    }

    /** @test @dataProvider invalidCellphones */
    public function validates_cellphone_format_to_send_a_token($cellphone)
    {
        Bus::fake();

        $this->json('POST', "/api/userActivation/token", with(['cellphone' => $cellphone]))
            ->assertStatus(422)
            ->assertJsonFragment(['cellphone']);

        Bus::assertNotDispatched(SendActivationToken::class);
    }

    /** @test */
    public function validates_user_exists_to_send_a_token()
    {
        Bus::fake();

        $this->json('POST', "/api/userActivation/token", with(['cellphone' => '4672839209']))
            ->assertStatus(404);

        Bus::assertNotDispatched(SendActivationToken::class);
    }

    /** @test */
    public function validates_cellphone_required_to_activate_the_user()
    {
        Bus::fake();

        $this->json('POST', "/api/userActivation", with(['token' => str_random(4)]))
            ->assertStatus(422)
            ->assertJsonFragment(['cellphone']);

        Bus::assertNotDispatched(UserActivate::class);
    }

    /** @test @dataProvider invalidCellphones */
    public function validates_cellphone_format_to_activate_the_user($cellphone)
    {
        Bus::fake();

        $this->json('POST', "/api/userActivation", with(['cellphone' => $cellphone]))
            ->assertStatus(422)
            ->assertJsonFragment(['cellphone']);

        Bus::assertNotDispatched(UserActivate::class);
    }

    /** @test */
    public function validates_user_exists_to_activate_the_user()
    {
        Bus::fake();

        $this->json('POST', "/api/userActivation", with(['cellphone' => '16987653190', 'token' => str_random(4)]))
            ->assertStatus(404);

        Bus::assertNotDispatched(UserActivate::class);
    }

    /** @test */
    public function validates_token_required_to_activate_the_user()
    {
        Bus::fake();

        $this->json('POST', "/api/userActivation", with(['cellphone' => '11926378221']))
            ->assertStatus(422)
            ->assertJsonFragment(['token']);

        Bus::assertNotDispatched(UserActivate::class);
    }

    /** @test */
    public function validates_token_must_be_a_string_to_activate_the_user()
    {
        Bus::fake();

        $this->json('POST', "/api/userActivation", with(['token' => 1292]))
            ->assertStatus(422)
            ->assertJsonFragment(['token']);

        Bus::assertNotDispatched(UserActivate::class);
    }

    /** @test */
    public function a_user_activate_its_account()
    {
        Bus::fake();

        $user = factory(User::class)->states('inactive')->create();
        $this->json('POST', "/api/userActivation",
                with(['cellphone' => $user->cellphone, 'token' => with($token = str_random(4))]))
            ->assertSuccessful()
            ->assertJsonFragment(['token']);

        Bus::assertDispatched(UserActivate::class, function ($job) use ($user, $token) {
            return $job->user->is($user) && $job->token === $token;
        });

        tap(Auth::guard('api-users')->user(), function($authUser) use ($user) {
            $this->assertTrue($authUser->is($user));
        });
    }

    /** @test */
    public function fails_activing_account_when_token_is_not_valid()
    {
        Bus::shouldReceive('dispatch')->once()
            ->with(anInstanceOf(UserActivate::class))
            ->andThrow(new InvalidActivationToken());

        $user = factory(User::class)->states('inactive')->create();

        $this->json('POST', "/api/userActivation",
                with(['cellphone' => $user->cellphone, 'token' => str_random(4)]))
            ->assertStatus(400)
            ->assertJsonFragment(['error']);

        $this->assertFalse(Auth::check());
    }

    /** @test */
    public function fails_activing_account_when_account_is_already_active()
    {
        $user = factory(User::class)->states('inactive')->create();

        Bus::shouldReceive('dispatch')->once()
            ->with(anInstanceOf(UserActivate::class))
            ->andThrow(new ModelAlreadyActive($user));

        $this->json('POST', "/api/userActivation",
                with(['cellphone' => $user->cellphone, 'token' => str_random(4)]))
            ->assertStatus(400)
            ->assertJsonFragment(['error']);

        $this->assertFalse(Auth::check());
    }

    // providers

    public function invalidCellphones() : array
    {
        return [
                ['invalidcellphone'],
                ['abcdefghijk'],
                ['abcdefghij'],
                ['a1952187626'],
                ['a195218762'],
                ['286438h283'],
                ['286438h283w'],
                ['119999090981'],
        ];
    }

}
