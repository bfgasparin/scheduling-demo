<?php

namespace Tests\Unit\Salon\Client;

use Carbon\Carbon;
use Tests\TestCase;
use App\Salon\Professional\WorkingJorney\Absence;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Test Salon Absence
 */
class AbsenceTest extends TestCase
{
    use DatabaseTransactions;

    /** @test **/
    public function a_absence_is_created_on_a_date() : void
    {
        $absence = factory(Absence::class)->create([
            'date' => with($date = date_random()),
        ]);

        tap($absence, function ($absence) use ($date) {
            $this->assertTrue($absence->isOnDate($date));
            $this->assertFalse($absence->isOnDate($date->copy()->subSecond()));
            $this->assertFalse($absence->isOnDate($date->copy()->subMinute()));
            $this->assertFalse($absence->isOnDate($date->copy()->subHour()));
            $this->assertFalse($absence->isOnDate($date->copy()->subDay()));
        });

        tap($absence, function ($absence) use ($date) {
            $this->assertTrue($absence->isOnDate($date));
            $this->assertTrue($absence->isOnDate($date->copy()->addSecond()));
            $this->assertTrue($absence->isOnDate($date->copy()->addMinute()));
            $this->assertTrue($absence->isOnDate($date->copy()->addHour()));
            $this->assertFalse($absence->isOnDate($date->copy()->addDay()));
        });
    }
}
