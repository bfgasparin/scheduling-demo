<?php

namespace Tests\Unit\Jobs\User;

use Bus;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Jobs\User\{Create as CreateUser, SendActivationToken};

/**
 * Tests of 'User Registration' Use Case
 */
class CreateTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_register_a_new_user_sucessfully()
    {
        Bus::fake();

        $data = factory(User::class)->maKe()->makeVisible('password')->toArray();

        tap((new CreateUser($data))->handle(), function ($user) use ($data) {
            Bus::assertDispatched(SendActivationToken::class, function ($job) use ($user) {
                return $job->user === $user;
            });
        });

        $this->assertDatabaseHas(
            'users',
            collect($data)->merge(['active' => false])
            ->except('password')->toArray()
        );
    }
}


