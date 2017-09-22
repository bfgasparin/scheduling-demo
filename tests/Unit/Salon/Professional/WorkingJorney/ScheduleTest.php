<?php

namespace Tests\Unit\Salon\Client;

use Carbon\Carbon;
use Tests\TestCase;
use App\Salon\Professional\WorkingJorney\Schedule;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Test Salon Schedule
 */
class ScheduleTest extends TestCase
{
    use DatabaseTransactions;

    /** @test **/
    public function a_schedule_is_created_on_a_date() : void
    {
        $schedule = factory(Schedule::class)->create([
            'date' => with($date = date_random()),
        ]);

        tap($schedule, function ($schedule) use ($date) {
            $this->assertTrue($schedule->isOnDate($date));
            $this->assertFalse($schedule->isOnDate($date->copy()->subSecond()));
            $this->assertFalse($schedule->isOnDate($date->copy()->subMinute()));
            $this->assertFalse($schedule->isOnDate($date->copy()->subHour()));
            $this->assertFalse($schedule->isOnDate($date->copy()->subDay()));
        });

        tap($schedule, function ($schedule) use ($date) {
            $this->assertTrue($schedule->isOnDate($date));
            $this->assertTrue($schedule->isOnDate($date->copy()->addSecond()));
            $this->assertTrue($schedule->isOnDate($date->copy()->addMinute()));
            $this->assertTrue($schedule->isOnDate($date->copy()->addHour()));
            $this->assertFalse($schedule->isOnDate($date->copy()->addDay()));
        });
    }
}
