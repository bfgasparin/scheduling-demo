<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;

/**
 * Test User instance
 */
class UserTest extends TestCase
{
    /** @test @dataProvider cellPhoneProvider */
    public function all_nexmo_notifications_is_routed_to_brasil($cellphone) : void
    {
        tap(new User(['cellphone' => $cellphone]), function ($user) use ($cellphone) {
            $this->assertEquals($cellphone, $user->cellphone);
            $this->assertEquals('55'.$cellphone, $user->routeNotificationForNexmo());
        });
    }

    /** @test */
    public function new_instance_has_default_attributes() : void
    {
        tap(new User([]), function ($user) {
            $this->assertEquals(false, $user->active);
        });
    }

    /** @test */
    public function new_instance_is_always_inactive() : void
    {
        tap(new User(['active' => true]), function ($user) {
            $this->assertEquals(false, $user->active);
        });
    }

    // providers
    public function cellPhoneProvider() : array
    {
        return [
            ['1192836271'],
            ['2287673463'],
            ['28987651231'],
        ];
    }
}
