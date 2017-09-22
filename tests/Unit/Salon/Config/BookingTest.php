<?php

namespace Tests\Unit\Salon\Config;

use App\Salon;
use Carbon\Carbon;
use Tests\TestCase;
use App\Salon\Employee;
use Illuminate\Support\Collection;
use App\Salon\Professional\WorkingJorney;
use Tests\Concerns\SalonCalendarHelpers;
use App\Salon\Config\Booking as ConfigBooking;
use App\Salon\Professional\WorkingJorney\Schedule;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Test Salon Config Booking
 */
class BookingTest extends TestCase
{
    use DatabaseTransactions,
        SalonCalendarHelpers;

    /** @test */
    public function a_config_booking_returns_the_same_calendar_interval_range_for_any_date() : void
    {
        $config = factory(ConfigBooking::class)->create([
            'salon_id' => $this->salon->id,
            'calendar_interval' => '20',
        ]);

        // Create professionais with the following entries and exits
        // Each line is a professional with the given entry and exit
        $this->createProfessionals([
            ['08:00:00', '17:00:00'],
            ['08:40:00', '18:20:00'],
            ['08:45:00', '19:45:00'],
            ['13:15:00', '20:00:00'],
        ]);

        // Then asserts that the calentar interval range begins with the early entry among
        // all professionals working Jorneys (08:00:00) and ends with the later exit (20:00:00)
        // for any date
        repeat(rand(1, 10), function () use ($config) {
            $this->assertEquals(
                [
                    '08:00:00', '08:20:00', '08:40:00', '09:00:00', '09:20:00', '09:40:00', '10:00:00', '10:20:00',
                    '10:40:00', '11:00:00', '11:20:00', '11:40:00', '12:00:00', '12:20:00', '12:40:00', '13:00:00',
                    '13:20:00', '13:40:00', '14:00:00', '14:20:00', '14:40:00', '15:00:00', '15:20:00', '15:40:00',
                    '16:00:00', '16:20:00', '16:40:00', '17:00:00', '17:20:00', '17:40:00', '18:00:00', '18:20:00',
                    '18:40:00', '19:00:00', '19:20:00', '19:40:00',
                ],
                $config->getCalendarIntervalRangeOn(date_random())->toArray()
            );
        });

        // Now with a diferent interval
        $config = factory(ConfigBooking::class)->create([
            'salon_id' => $this->salon->id,
            'calendar_interval' => '15'
        ]);

        // Create professionais with the following entries and exits.
        // Each line is a professional with the given entry and exit
        $this->createProfessionals([
            ['07:10:00', '17:00:00'],
            ['08:00:00', '18:40:00'],
            ['08:45:00', '18:15:00'],
            ['11:10:00', '21:00:00'],
            ['13:00:00', '21:20:00'],
        ]);

        // Then asserts that the calentar interval range begins with the early entry among
        // all professionals working Jorneys (07:15:00) and ends with the nearst later exit (21:20:00)
        // for any date
        repeat(rand(1, 10), function () use ($config) {
            $this->assertEquals(
                [
                    '07:10:00', '07:25:00', '07:40:00', '07:55:00', '08:10:00', '08:25:00', '08:40:00', '08:55:00',
                    '09:10:00', '09:25:00', '09:40:00', '09:55:00', '10:10:00', '10:25:00', '10:40:00', '10:55:00',
                    '11:10:00', '11:25:00', '11:40:00', '11:55:00', '12:10:00', '12:25:00', '12:40:00', '12:55:00',
                    '13:10:00', '13:25:00', '13:40:00', '13:55:00', '14:10:00', '14:25:00', '14:40:00', '14:55:00',
                    '15:10:00', '15:25:00', '15:40:00', '15:55:00', '16:10:00', '16:25:00', '16:40:00', '16:55:00',
                    '17:10:00', '17:25:00', '17:40:00', '17:55:00', '18:10:00', '18:25:00', '18:40:00', '18:55:00',
                    '19:10:00', '19:25:00', '19:40:00', '19:55:00', '20:10:00', '20:25:00', '20:40:00', '20:55:00',
                    '21:10:00',
                ],
                $config->getCalendarIntervalRangeOn(date_random())->toArray()
            );
        });
    }

    /** @test */
    public function a_config_booking_returns_a_calendar_interval_range_from_professionals_schedules() : void
    {
        $config = factory(ConfigBooking::class)->create([
            'salon_id' => $this->salon->id,
            'calendar_interval' => '30'
        ]);

        tap(date_random(), function ($date) use ($config) {
            // Create professionais schedules wirh the following dates, entries and exits
            // Each line is a professional with a schedule with the given date, entry and exit
            $this->createProfessionalsWithSchedules([
                [$date, '09:00:00', '16:00:00'],
                [$date, '08:00:00', '18:20:00'],
                [$date, '08:00:00', '19:00:00'],
                [$date, '13:00:00', '20:00:00'],
            ]);

            // Asserts that the calentar interval range begins with the early entry among
            // schedules of all professionals of the salon (08:00:00) and ends with the later exit (20:00:00)
            // on the given date
            $this->assertEquals(
                [
                    '08:00:00', '08:30:00', '09:00:00', '09:30:00', '10:00:00', '10:30:00', '11:00:00', '11:30:00',
                    '12:00:00', '12:30:00', '13:00:00', '13:30:00', '14:00:00', '14:30:00', '15:00:00', '15:30:00',
                    '16:00:00', '16:30:00', '17:00:00', '17:30:00', '18:00:00', '18:30:00', '19:00:00', '19:30:00',
                ],
                $config->getCalendarIntervalRangeOn($date)->toArray()
            );
        });

        // Now with a diferent interval
        $this->salon->employees->each->delete();
        $config = factory(ConfigBooking::class)->create([
            'salon_id' => $this->salon->id,
            'calendar_interval' => '15'
        ]);

        tap(date_random(), function ($date) use ($config) {
            // Create professionais schedules wirh the following dates, entries and exits
            // Each line is a professional with a schedule with the given date, entry and exit
            $this->createProfessionalsWithSchedules([
                [$date, '08:10:00', '17:00:00'],
                [$date, '08:20:00', '18:40:00'],
                [$date, '08:45:00', '18:15:00'],
                [$date, '11:10:00', '21:00:00'],
                [$date, '13:00:00', '21:20:00'],
                [$date, '11:00:00', '22:20:00'],
            ]);

            // Then asserts that the calentar interval range begins with the early entry among
            // all professionals working Jorneys (07:15:00) and ends with the nearst later exit (21:20:00)
            // for any date
            $this->assertEquals(
                [
                    '08:10:00', '08:25:00', '08:40:00', '08:55:00', '09:10:00', '09:25:00', '09:40:00', '09:55:00',
                    '10:10:00', '10:25:00', '10:40:00', '10:55:00', '11:10:00', '11:25:00', '11:40:00', '11:55:00',
                    '12:10:00', '12:25:00', '12:40:00', '12:55:00', '13:10:00', '13:25:00', '13:40:00', '13:55:00',
                    '14:10:00', '14:25:00', '14:40:00', '14:55:00', '15:10:00', '15:25:00', '15:40:00', '15:55:00',
                    '16:10:00', '16:25:00', '16:40:00', '16:55:00', '17:10:00', '17:25:00', '17:40:00', '17:55:00',
                    '18:10:00', '18:25:00', '18:40:00', '18:55:00', '19:10:00', '19:25:00', '19:40:00', '19:55:00',
                    '20:10:00', '20:25:00', '20:40:00', '20:55:00', '21:10:00', '21:25:00', '21:40:00', '21:55:00',
                    '22:10:00',
                ],
                $config->getCalendarIntervalRangeOn($date)->toArray()
            );
        });
    }

    /** @test */
    public function a_cancel_tolerance_for_client_user_is_exceeded() : void
    {
        $config = factory(ConfigBooking::class)->create([
            'cancel_tolerance_for_client_user' => $time = rand(1, 60),
        ]);

        // teste with a time one minute earlier then cancel tolerance time
        tap(Carbon::now()->subMinutes($time - 1), function ($limitTime) use ($config) {
            $this->assertTrue(
                $config->isCancelToleranceForClientUserExceededWith($limitTime->toDateString(), $limitTime->toTimeString())
            );
        });

        // ... then test with a random time earlier then cancel tolerance time
        tap(Carbon::now()->subMinutes($time - rand(1, $time)), function ($time) use ($config) {
            $this->assertTrue(
                $config->isCancelToleranceForClientUserExceededWith($time->toDateString(), $time->toTimeString())
            );
        });
    }

    /** @test */
    public function a_cancel_tolerance_for_client_user_is_not_exceeded() : void
    {
        $config = factory(ConfigBooking::class)->create([
            'cancel_tolerance_for_client_user' => $time = rand(1, 60),
        ]);

        // teste with a time one minute later then cancel tolerance time
        tap(Carbon::now()->subMinutes($time + 1), function ($limitTime) use ($config) {
            $this->assertFalse(
                $config->isCancelToleranceForClientUserExceededWith($limitTime->toDateString(), $limitTime->toTimeString())
            );
        });

        // ... then test with a random time later then cancel tolerance time
        tap(Carbon::now()->subMinutes($time + rand(1, 108000)), function ($time) use ($config) {
            $this->assertFalse(
                $config->isCancelToleranceForClientUserExceededWith($time->toDateString(), $time->toTimeString())
            );
        });
    }

    /** @test */
    public function a_create_tolerance_for_client_user_is_exceeded() : void
    {
        $config = factory(ConfigBooking::class)->create([
            'create_tolerance_for_client_user' => $time = rand(1, 60),
        ]);

        // teste with a time one minute earlier then create tolerance time
        tap(Carbon::now()->subMinutes($time - 1), function ($limitTime) use ($config) {
            $this->assertTrue(
                $config->isCreateToleranceForClientUserExceededWith($limitTime->toDateString(), $limitTime->toTimeString())
            );
        });

        // ... then test with a random time earlier then create tolerance time
        tap(Carbon::now()->subMinutes($time - rand(1, $time)), function ($time) use ($config) {
            $this->assertTrue(
                $config->isCreateToleranceForClientUserExceededWith($time->toDateString(), $time->toTimeString())
            );
        });
    }

    /** @test */
    public function a_create_tolerance_for_client_user_is_not_exceeded() : void
    {
        $config = factory(ConfigBooking::class)->create([
            'create_tolerance_for_client_user' => $time = rand(1, 60),
        ]);

        // teste with a time one minute later then create tolerance time
        tap(Carbon::now()->subMinutes($time + 1), function ($limitTime) use ($config) {
            $this->assertFalse(
                $config->isCreateToleranceForClientUserExceededWith($limitTime->toDateString(), $limitTime->toTimeString())
            );
        });

        // ... then test with a random time later then create tolerance time
        tap(Carbon::now()->subMinutes($time + rand(1, 108000)), function ($time) use ($config) {
            $this->assertFalse(
                $config->isCreateToleranceForClientUserExceededWith($time->toDateString(), $time->toTimeString())
            );
        });
    }

    /**
     * Create random professionals on the given $salon for each instance in $entriesAndExits array
     *
     * Each instance in given $entriesAndExits is a professional with a schedule with the given
     * date, entry and exit
     *
     * @param Salon $salon
     * @param array $entriesAndExits An array containing entries and exits
     *
     * @return void
     */
    public function createProfessionals(array $entriesAndExits) : void
    {
        foreach ($entriesAndExits as [$entry, $exit]) {
            $this->createProfessionalWithWorkingJorney([
                'entry' => $entry,
                'exit' => $exit,
            ]);
        }
    }

    /**
     * Create random professionals with schedules for each instance in $datesEntriesAndExits array
     *
     * Each instance in given $datesEntriesAndExits is a professional with a schedule with the given date, entry
     * and exit
     *
     * @param array $datesEntriesAndExits An array containing dates, entries and exits
     *
     * @return void
     */
    public function createProfessionalsWithSchedules(array $datesEntriesAndExits) : void
    {
        foreach ($datesEntriesAndExits as $dateEntryExit) {
            $this->createProfessionalWithSchedules([], [$dateEntryExit]);
        }
    }
}
