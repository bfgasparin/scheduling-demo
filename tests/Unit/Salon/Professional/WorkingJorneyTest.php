<?php

namespace Tests\Unit\Salon\Professional;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Collection;
use App\Salon\Professional\WorkingJorney;
use App\Salon\Professional\WorkingJorney\Schedule;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Test Professional WorkingJorney
 */
class WorkingJorneyTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_working_jorney_can_represents_a_bunch_of_dates() : void
    {
        $workingJorney = factory(WorkingJorney::class)->create([
            'days_of_week' => [Carbon::MONDAY, Carbon::TUESDAY, Carbon::THURSDAY, Carbon::SATURDAY]
        ]);

        tap($workingJorney->fresh(), function ($workingJorney) {
            $monday = Carbon::today()->startOfWeek();

            beat($workingJorney, rand(2,5), function ($workingJorney) use ($monday) {
                $this->assertTrue($workingJorney->representsDate($monday));
                $this->assertTrue($workingJorney->representsDate($monday->next(Carbon::TUESDAY)));
                $this->assertFalse($workingJorney->representsDate($monday->next(Carbon::WEDNESDAY)));
                $this->assertTrue($workingJorney->representsDate($monday->next(Carbon::THURSDAY)));
                $this->assertFalse($workingJorney->representsDate($monday->next(Carbon::FRIDAY)));
                $this->assertTrue($workingJorney->representsDate($monday->next(Carbon::SATURDAY)));
                $this->assertFalse($workingJorney->representsDate($monday->next(Carbon::SUNDAY)));
                $this->assertTrue($workingJorney->representsDate($monday->next(Carbon::MONDAY)));
            });
        });

        $workingJorney = factory(WorkingJorney::class)->create([
            'days_of_week' => [Carbon::WEDNESDAY, Carbon::THURSDAY, Carbon::FRIDAY, Carbon::SATURDAY, Carbon::SUNDAY]
        ]);

        tap($workingJorney->fresh(), function ($workingJorney) {
            $monday = Carbon::today()->startOfWeek();

            beat($workingJorney, rand(2,5), function ($workingJorney) use ($monday) {
                $this->assertFalse($workingJorney->representsDate($monday));
                $this->assertFalse($workingJorney->representsDate($monday->next(Carbon::TUESDAY)));
                $this->assertTrue($workingJorney->representsDate($monday->next(Carbon::WEDNESDAY)));
                $this->assertTrue($workingJorney->representsDate($monday->next(Carbon::THURSDAY)));
                $this->assertTrue($workingJorney->representsDate($monday->next(Carbon::FRIDAY)));
                $this->assertTrue($workingJorney->representsDate($monday->next(Carbon::SATURDAY)));
                $this->assertTrue($workingJorney->representsDate($monday->next(Carbon::SUNDAY)));
                $this->assertFalse($workingJorney->representsDate($monday->next(Carbon::MONDAY)));
            });
        });
    }

    /** @test */
    public function a_working_jorney_returns_the_same_calendar_interval_range_for_any_date() : void
    {
        $workingJorney = factory(WorkingJorney::class)->create([
            'entry' => '06:20:00',
            'exit' => '18:00:00',
            'calendar_interval' => '20'
        ]);

        // asserts the range is equal for any date
        repeat(rand(1,10), function () use ($workingJorney) {
            $this->assertEquals(
                [
                    '06:20:00', '06:40:00', '07:00:00', '07:20:00', '07:40:00', '08:00:00', '08:20:00', '08:40:00',
                    '09:00:00', '09:20:00', '09:40:00', '10:00:00', '10:20:00', '10:40:00', '11:00:00', '11:20:00',
                    '11:40:00', '12:00:00', '12:20:00', '12:40:00', '13:00:00', '13:20:00', '13:40:00', '14:00:00',
                    '14:20:00', '14:40:00', '15:00:00', '15:20:00', '15:40:00', '16:00:00', '16:20:00', '16:40:00',
                    '17:00:00', '17:20:00', '17:40:00'
                ],
                $workingJorney->fresh()->getCalendarIntervalRangeOn(date_random())->toArray()
            );
        });

        $workingJorney = factory(WorkingJorney::class)->create([
            'entry' => '06:30:00',
            'exit' => '17:45:00',
            'calendar_interval' => '15'
        ]);

        repeat(rand(1,10), function () use ($workingJorney) {
            $this->assertEquals(
                [
                    '06:30:00', '06:45:00', '07:00:00', '07:15:00', '07:30:00', '07:45:00', '08:00:00', '08:15:00',
                    '08:30:00', '08:45:00', '09:00:00', '09:15:00', '09:30:00', '09:45:00', '10:00:00', '10:15:00',
                    '10:30:00', '10:45:00', '11:00:00', '11:15:00', '11:30:00', '11:45:00', '12:00:00', '12:15:00',
                    '12:30:00', '12:45:00', '13:00:00', '13:15:00', '13:30:00', '13:45:00', '14:00:00', '14:15:00',
                    '14:30:00', '14:45:00', '15:00:00', '15:15:00', '15:30:00', '15:45:00', '16:00:00', '16:15:00',
                    '16:30:00', '16:45:00', '17:00:00', '17:15:00', '17:30:00',
                ],
                $workingJorney->fresh()->getCalendarIntervalRangeOn(date_random())->toArray()
            );
        });
    }

    /** @test */
    public function a_working_jorney_returns_a_calendar_interval_range_from_a_registered_schedule() : void
    {
        $workingJorney = factory(WorkingJorney::class)->create([
            'entry' => '06:20:00',
            'exit' => '18:00:00',
            'calendar_interval' => '20'
        ]);

        tap(date_random(), function ($date) use ($workingJorney){
            $schedule = factory(Schedule::class)->create([
                'working_jorney_id' => $workingJorney->id,
                'entry' => '08:00:00',
                'exit' => '17:00:00',
                'date' => $date,
            ]);

            $this->assertEquals(
                [
                    '08:00:00', '08:20:00', '08:40:00', '09:00:00', '09:20:00', '09:40:00', '10:00:00', '10:20:00',
                    '10:40:00', '11:00:00', '11:20:00', '11:40:00', '12:00:00', '12:20:00', '12:40:00', '13:00:00',
                    '13:20:00', '13:40:00', '14:00:00', '14:20:00', '14:40:00', '15:00:00', '15:20:00', '15:40:00',
                    '16:00:00', '16:20:00', '16:40:00',
                ],
                $workingJorney->fresh()->getCalendarIntervalRangeOn($date)->toArray()
            );
        });

        tap(date_random(), function ($date) use ($workingJorney){
            $schedule = factory(Schedule::class)->create([
                'working_jorney_id' => $workingJorney->id,
                'entry' => '06:00:00',
                'exit' => '19:40:00',
                'date' => $date,
            ]);

            $this->assertEquals(
                [
                    '06:00:00', '06:20:00', '06:40:00', '07:00:00', '07:20:00', '07:40:00', '08:00:00', '08:20:00',
                    '08:40:00', '09:00:00', '09:20:00', '09:40:00', '10:00:00', '10:20:00', '10:40:00', '11:00:00',
                    '11:20:00', '11:40:00', '12:00:00', '12:20:00', '12:40:00', '13:00:00', '13:20:00', '13:40:00',
                    '14:00:00', '14:20:00', '14:40:00', '15:00:00', '15:20:00', '15:40:00', '16:00:00', '16:20:00',
                    '16:40:00', '17:00:00', '17:20:00', '17:40:00', '18:00:00', '18:20:00', '18:40:00', '19:00:00',
                    '19:20:00',
                ],
                $workingJorney->fresh()->getCalendarIntervalRangeOn($date)->toArray()
            );
        });
    }
}
