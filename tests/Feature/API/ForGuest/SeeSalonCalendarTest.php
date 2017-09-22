<?php

namespace Tests\Feature\API\ForGuest;

use App\Salon;
use Tests\TestCase;
use App\Salon\Employee;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Testing a non authenticated user seeing a salon calendar
 */
class SeeSalonCalendarTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_guest_can_see_calendar_from_a_salon() : void
    {
        $this->markTestSkipped("Not implemented yet");
    }
}
