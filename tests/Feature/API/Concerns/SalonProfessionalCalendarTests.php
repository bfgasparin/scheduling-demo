<?php

namespace Tests\Feature\API\Concerns;

use App\Salon;
use Carbon\Carbon;
use App\Salon\Employee;
use Tests\Concerns\SalonCalendarHelpers;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Contains feature tests related to Salon Professional Calendar
 */
trait SalonProfessionalCalendarTests
{
    use DatabaseTransactions,
        SalonCalendarHelpers;

    /**
     * Returns the calendar url for the given professional
     *
     * @param Employee $professional
     *
     * @return string
     */
    public function urlFor(Employee $professional) : string
    {
        $urlMethod = "url";
        if(method_exists($this, $urlMethod)) {
            return $this->$urlMethod($professional);
        }

        return "professionals/{$professional->id}/calendar";
    }

    /** @test */
    public function see_empty_calendar_from_a_professional_that_misses_working_jorney() : void
    {
        $professional = $this->createProfessional([
            'salon_id' => $this->salon->id,
        ]);

        // setup application before test
        $this->setUpApplication($professional);

        $this->json('GET', "/api/{$this->urlFor($professional)}")
            ->assertSuccessful()
            ->assertExactJson([]);
    }

    /** @test */
    public function see_empty_calendar_from_a_professional_that_does_not_work_on_current_date() : void
    {
        $today = Carbon::today();

        $professional = $this->createProfessionalWithWorkingJorney([
            'calendar_interval' => 15,
            'entry' => '09:00:00',
            'exit' => '14:30:00',
            'days_of_week' => [$today->tomorrow()->dayOfWeek, $today->nextWeekendDay()->dayOfWeek],
        ]);

        // setup application before test
        $this->setUpApplication($professional);

        $this->json('GET', "/api/{$this->urlFor($professional)}")
            ->assertSuccessful()
            ->assertExactJson([]);
    }

    /** @test */
    public function see_empty_calendar_from_a_professional_that_does_not_work_on_the_given_date() : void
    {
        $this->markTestSkipped("Not implemented yet");
    }

    /** @test */
    public function do_not_see_calendar_items_for_a_professional_on_dates_the_professional_does_not_work() : void
    {
        $this->markTestSkipped("Not implemented yet");
    }

    /** @test */
    public function see_empty_calendar_from_a_professional_that_abcenses_on_current_date() : void
    {
        $this->markTestSkipped("Not implemented yet");
    }

    /** @test */
    public function do_not_see_calendar_items_for_a_professional_on_dates_the_professional_absences() : void
    {
        $this->markTestSkipped("Not implemented yet");
    }

    /** @test */
    public function see_a_calendar_of_a_professional_on_current_date() : void
    {
        $today = Carbon::today();

        $professional = $this->createProfessionalWithWorkingJorney([
            'calendar_interval' => 30,
            'entry' => '13:00:00',
            'exit' => '14:30:00',
            'days_of_week' => [$today->dayOfWeek]
        ])->makeHidden('services');

        // setup application before test
        $this->setUpApplication($professional);

        $this->json('GET', "/api/{$this->urlFor($professional)}")
            ->assertSuccessful()
            ->assertExactJson([
                [
                    'id' => "{$today->timestamp}130000{$professional->id}",
                    'date' => $today->toDateString(),
                    'interval' => '13:00:00',
                    'professional' => $professional->toArray(),
                    'bookings' => [],
                    'blocked' => false,
                    'description' => 'Available',
                    'available_services' => $professional->services->pluck('id')->toArray(),
                ],
                [
                    'id' => "{$today->timestamp}133000{$professional->id}",
                    'date' => $today->toDateString(),
                    'interval' => '13:30:00',
                    'professional' => $professional->toArray(),
                    'bookings' => [],
                    'blocked' => false,
                    'description' => 'Available',
                    'available_services' => $professional->services->pluck('id')->toArray(),
                ],
                [
                    'id' => "{$today->timestamp}140000{$professional->id}",
                    'date' => $today->toDateString(),
                    'interval' => '14:00:00',
                    'professional' => $professional->toArray(),
                    'bookings' => [],
                    'blocked' => false,
                    'description' => 'Available',
                    'available_services' => $professional->services->pluck('id')->toArray(),
                ],
            ]);
    }

    /** @test */
    public function see_a_calendar_of_a_professional_on_the_given_date() : void
    {
        $this->markTestSkipped("Not implemented yet");
    }

    /** @test */
    public function see_a_calendar_of_a_professional_with_full_calendar_items() : void
    {
        $today = Carbon::today();

        $professional = $this->createProfessionalWithWorkingJorney([
            'calendar_interval' => 30,
            'entry' => '10:00:00',
            'exit' => '12:00:00',
            'days_of_week' => [$today->dayOfWeek]
        ])->makeHidden(['services', 'bookings']);

        [$booking1, $booking2] = $this->createBookingsForProfessional(
            $professional,
            [
                [$today, '10:00:00'], // date and start of Booking 1
                [$today, '11:00:00']  // date and start of Booking 2
            ]
        );

        // setup application before test
        $this->setUpApplication($professional);

        $this->json('GET', "/api/{$this->urlFor($professional)}")
            ->assertSuccessful()
            ->assertExactJson([
                [
                    'id' => "{$today->timestamp}100000{$professional->id}",
                    'date' => $today->toDateString(),
                    'interval' => '10:00:00',
                    'professional' => $professional->toArray(),
                    'bookings' => [$booking1->toArray()],
                    'blocked' => true,
                    'description' => 'Full Calendar Item',
                    'available_services' => [],
                ],
                [
                    'id' => "{$today->timestamp}103000{$professional->id}",
                    'date' => $today->toDateString(),
                    'interval' => '10:30:00',
                    'professional' => $professional->toArray(),
                    'bookings' => [],
                    'blocked' => false,
                    'description' => 'Available',
                    'available_services' => $professional->services->pluck('id')->toArray(),
                ],
                [
                    'id' => "{$today->timestamp}110000{$professional->id}",
                    'date' => $today->toDateString(),
                    'interval' => '11:00:00',
                    'professional' => $professional->toArray(),
                    'bookings' => [$booking2->toArray()],
                    'blocked' => true,
                    'description' => 'Full Calendar Item',
                    'available_services' => [],
                ],
                [
                    'id' => "{$today->timestamp}113000{$professional->id}",
                    'date' => $today->toDateString(),
                    'interval' => '11:30:00',
                    'professional' => $professional->toArray(),
                    'bookings' => [],
                    'blocked' => false,
                    'description' => 'Available',
                    'available_services' => $professional->services->pluck('id')->toArray(),
                ],
            ]);
    }

    /** @test */
    public function see_a_calendar_of_a_professional_with_a_custom_jorney_on_current_date() : void
    {
        $today = Carbon::today();
        $professional = $this->createProfessionalWithSchedules(
            [
                'calendar_interval' => 45,
                'entry' => '14:00:00',
                'exit' => '15:55:00',
                'days_of_week' => [$today->dayOfWeek]
            ],
            [
                [$today, '09:00:00', '12:00:00'], // date, entry and exit of a Schedule
            ]
        )->makeHidden(['services', 'bookings', 'workingJorney']);

        [$booking] = $this->createBookingsForProfessional(
            $professional,
            [
                [$today, '09:00:00'], // date and start of Booking
            ]
        );

        // setup application before test
        $this->setUpApplication($professional);

        $this->json('GET', "/api/{$this->urlFor($professional)}")
            ->assertSuccessful()
            ->assertExactJson([
                [
                    'id' => "{$today->timestamp}090000{$professional->id}",
                    'date' => $today->toDateString(),
                    'interval' => '09:00:00',
                    'professional' => $professional->toArray(),
                    'bookings' => [$booking->toArray()],
                    'blocked' => true,
                    'description' => 'Full Calendar Item',
                    'available_services' => [],
                ],
                [
                    'id' => "{$today->timestamp}094500{$professional->id}",
                    'date' => $today->toDateString(),
                    'interval' => '09:45:00',
                    'professional' => $professional->toArray(),
                    'bookings' => [],
                    'blocked' => false,
                    'description' => 'Available',
                    'available_services' => $professional->services->pluck('id')->toArray(),
                ],
                [
                    'id' => "{$today->timestamp}103000{$professional->id}",
                    'date' => $today->toDateString(),
                    'interval' => '10:30:00',
                    'professional' => $professional->toArray(),
                    'bookings' => [],
                    'blocked' => false,
                    'description' => 'Available',
                    'available_services' => $professional->services->pluck('id')->toArray(),
                ],
                [
                    'id' => "{$today->timestamp}111500{$professional->id}",
                    'date' => $today->toDateString(),
                    'interval' => '11:15:00',
                    'professional' => $professional->toArray(),
                    'bookings' => [],
                    'blocked' => false,
                    'description' => 'Available',
                    'available_services' => $professional->services->pluck('id')->toArray(),
                ],
            ]);
    }

    /** @test */
    public function see_a_calendar_of_a_professional_with_a_custom_jorney_on_the_given_date() : void
    {
        $this->markTestSkipped("Not implemented yet");
    }

    /** @test */
    public function see_a_calendar_of_a_professional_with_a_custom_jorney_on_different_dates() : void
    {
        $this->markTestSkipped("Not implemented yet");
    }

    /**
     * Setup the application before run the tests
     *
     * You create fixtures here, authenticated to the app, and do whatever needed bebore run the test
     * against the given professional
     *
     * @param Employee $professional  The professional to test the calendar
     * @return self
     */
    public function setUpApplication(Employee $professional) : self
    {
        $setUpMethod = "setUpAppBeforeTest";
        if(method_exists($this, $setUpMethod)) {
            $this->$setUpMethod($professional);
        }

        return $this;
    }
}
