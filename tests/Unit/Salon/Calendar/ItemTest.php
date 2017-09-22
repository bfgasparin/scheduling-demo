<?php

namespace Tests\Unit\Salon\Calendar;

use App\Salon;
use Carbon\Carbon;
use Tests\TestCase;
use App\Salon\Client\Booking;
use Illuminate\Support\Collection;
use App\Salon\Client\BookingCollection;
use App\Salon\{Client, Employee, Service};
use Tests\Concerns\SalonCalendarHelpers;
use App\Salon\Calendar\Item as CalendarItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Test Calendar Item
 */
class ItemTest extends TestCase
{
    use DatabaseTransactions,
        SalonCalendarHelpers;

    /**
     * The client used on booking instances
     * @var App\Salon\Client
     */
    protected $client;

    /** @before */
    public function setUpClient() : void
    {
        $this->client = factory(Client::class)->create();
    }

    /**
     * Returns a Salon to be used on test
     * @see Tests\Concerns\SalonCalendarHelpers
     *
     * @return App\Salon
     */
    public function salon() : Salon
    {
        return $this->client->salon;
    }


    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function instance_accepts_only_professional_employee() : void
    {
        new CalendarItem(
            date_random(),
            time_random(),
            factory(Employee::class)->states('not_professional')->create(),
            new BookingCollection([])
        );
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function instance_accepts_only_bookings_from_the_given_professional() : void
    {
        new CalendarItem(
            Carbon::today(),
            time_random(),
            $this->createProfessionalWithWorkingJorney(),
            factory(Booking::class, rand(1, 5))->create()
        );
    }

    public function instance_accepts_only_bookings_of_the_given_date() : void
    {
        // TODO
    }

    public function instance_accepts_only_bookings_of_the_given_interval() : void
    {
        // TODO
    }

    /** @test */
    public function a_calendar_item_can_be_instanciated() : void
    {
        $calendarItem = new CalendarItem(
            with($date = date_random()),
            with($interval = time_random()),
            with($professional = $this->createProfessionalWithWorkingJorney()),
            with($bookings = factory(Booking::class, 1)->create([
                'client_id' => $this->client->id,
                'professional_id' => $professional->id
            ]))
        );

        tap($calendarItem, function ($calendarItem) use ($date, $interval, $professional, $bookings) {
            $this->assertNotNull($calendarItem->id);
            $this->assertEquals($date, $calendarItem->date);
            $this->assertEquals($interval, $calendarItem->interval);
            $this->assertTrue($calendarItem->professional->is($professional));
            $this->assertEmpty($calendarItem->bookings->diff($bookings));
        });
    }

    /** @test **/
    public function a_calendar_item_belongs_to_a_date() : void
    {
        $calendarItem = new CalendarItem(
            with($date = date_random()),
            time_random(),
            with($professional = $this->createProfessionalWithWorkingJorney()),
            factory(Booking::class, 1)->create([
                'client_id' => $this->client->id,
                'professional_id' => $professional->id
            ])
        );

        tap($calendarItem, function ($calendarItem) use ($date) {
            $this->assertTrue($calendarItem->isOnDate($date));
            $this->assertFalse($calendarItem->isOnDate($date->copy()->subSecond()));
            $this->assertFalse($calendarItem->isOnDate($date->copy()->subMinute()));
            $this->assertFalse($calendarItem->isOnDate($date->copy()->subHour()));
            $this->assertFalse($calendarItem->isOnDate($date->copy()->subDay()));
        });

        tap($calendarItem, function ($calendarItem) use ($date) {
            $this->assertTrue($calendarItem->isOnDate($date));
            $this->assertTrue($calendarItem->isOnDate($date->copy()->addSecond()));
            $this->assertTrue($calendarItem->isOnDate($date->copy()->addMinute()));
            $this->assertTrue($calendarItem->isOnDate($date->copy()->addHour()));
            $this->assertFalse($calendarItem->isOnDate($date->copy()->addDay()));
        });
    }

    /** @test **/
    public function a_calendar_item_is_belongs_to_an_interval() : void
    {
        $calendarItem = new CalendarItem(
            date_random(),
            with($interval = time_random()),
            with($professional = $this->createProfessionalWithWorkingJorney()),
            factory(Booking::class, 1)->create([
                'client_id' => $this->client->id,
                'professional_id' => $professional->id
            ])
        );

        tap($calendarItem, function ($calendarItem) use ($interval) {
            $this->assertTrue($calendarItem->isOnInterval($interval));
            $this->assertFalse($calendarItem->isOnInterval(time_random()));
        });
    }

    /** @test **/
    public function a_calendar_item_belongs_to_a_professional() : void
    {
        $calendarItem = new CalendarItem(
            date_random(),
            time_random(),
            with($professional = $this->createProfessionalWithWorkingJorney()),
            factory(Booking::class, 1)->create([
                'client_id' => $this->client->id,
                'professional_id' => $professional->id
            ])
        );

        tap($calendarItem, function ($calendarItem) use ($professional) {
            $this->assertTrue($calendarItem->hasProfessional($professional));
            $this->assertFalse($calendarItem->hasProfessional($this->createProfessional()));
        });
    }

    /** @test */
    public function a_calendar_item_id_is_unique() : void
    {
        $calendarItems = Collection::times(rand(40, 100), function () {
            return new CalendarItem(
                date_random(),
                time_random(),
                with($professional = $this->createProfessionalWithWorkingJorney()->fresh()),
                factory(Booking::class, 1)->create([
                    'client_id' => $this->client->id,
                    'professional_id' => $professional->id,
                    'service_id' => $professional->services->random()->id,
                ])
            );
        });

        $this->assertEquals($calendarItems->count(), $calendarItems->unique('id')->count());
    }

    /** @test */
    public function a_calendar_can_be_full() : void
    {
        $date = Carbon::today()->addDays(rand(-60, 60));
        $professional = $this->createProfessionalWithWorkingJorney([
            'calendar_interval' => $interval = '15',
            'entry' => $entry = '09:00:00',
            'exit' => $exit = '20:00:00',
            'days_of_week' => [$date->dayOfWeek],
        ]);

        // Create a booking for the professional
        $bookings = factory(Booking::class, 1)->create([
            'date' => $date,
            'start' => $start = time_random_between($entry, $exit, $interval),
            'client_id' => $this->client->id,
            'professional_id' => $professional->id,
            'service_id' => $professional->services->random()->id,
        ]);

        // ... and then create a calendar item with same date and inteval than the booking
        // of the professional
        tap(new CalendarItem($date, $start, $professional, $bookings), function ($calendarItem) {
            $this->assertEquals('Full Calendar Item', $calendarItem->description);
            $this->assertTrue($calendarItem->blocked);
            $this->assertEmpty($calendarItem->available_services);
        });
    }

    /** @test */
    public function a_calendar_item_is_arrayable() : void
    {
        $calendarItem = new CalendarItem(
            Carbon::parse('2017-06-26'),
            '16:14:00',
            with($professional = $this->createProfessionalWithWorkingJorney()),
            with($bookings = factory(Booking::class, 1)->create([
                'client_id' => $this->client->id,
                'professional_id' => $professional->id,
                'service_id' => $professional->services->random()->id,
            ]))
        );

        $this->assertEquals(
            [
                'id' => "1498446000161400{$professional->id}",
                'date' => '2017-06-26',
                'interval' => '16:14:00',
                'professional' => $professional->makeHidden(['services', 'bookings'])->toArray(),
                'bookings' => $bookings->toArray(),
                'blocked' => false,
                'description' => 'Available',
                'available_services' => $professional->services->pluck('id')->toArray(),
            ],
            $calendarItem->toArray()
        );
    }

    /** @test */
    public function a_calendar_item_is_array_accessable() : void
    {
        $calendarItem = new CalendarItem(
            Carbon::parse('2017-06-26'),
            '16:14:00',
            with($professional = $this->createProfessionalWithWorkingJorney()),
            factory(Booking::class, 1)->create([
                'client_id' => $this->client->id,
                'professional_id' => $professional->id,
                'service_id' => $professional->services->random()->id,
            ])
        );

        tap($calendarItem, function ($calendarItem) {
            $this->assertEquals($calendarItem->id, $calendarItem['id']);
            $this->assertEquals($calendarItem->date, $calendarItem['date']);
            $this->assertEquals($calendarItem->interval, $calendarItem['interval']);
            $this->assertEquals($calendarItem->professional, $calendarItem['professional']);
            $this->assertEquals($calendarItem->bookings, $calendarItem['bookings']);
            $this->assertEquals($calendarItem->blocked, $calendarItem['blocked']);
            $this->assertEquals($calendarItem->description, $calendarItem['description']);
            $this->assertEquals($calendarItem->available_services, $calendarItem['available_services']);
        });
    }

    /** @test */
    public function a_calendar_item_is_jsonable() : void
    {
        $calendarItem = new CalendarItem(
            Carbon::parse('2017-06-26'),
            '16:14:00',
            with($professional = $this->createProfessionalWithWorkingJorney()->fresh()),
            with($bookings = factory(Booking::class, 1)->create([
                'client_id' => $this->client->id,
                'professional_id' => $professional->id,
                'service_id' => $professional->services->random()->id,
            ]))
        );

        $this->assertEquals(
            '{"id":"1498446000161400'.$professional->id.'",'.
            '"date":"2017-06-26","interval":"16:14:00","professional":'.
            $professional->makeHidden(['services', 'bookings'])->toJson().','.
            '"bookings":'.$bookings->toJson().',"blocked":false,"description":"Available",'.
            '"available_services":'.$professional->services->pluck('id')->toJson().'}',
            $calendarItem->toJson()
        );
    }

    /** @test */
    public function a_calendar_item_is_json_serializable() : void
    {
        $calendarItem = new CalendarItem(
            Carbon::parse('2017-06-26'),
            '16:14:00',
            with($professional = $this->createProfessionalWithWorkingJorney()),
            with($bookings = factory(Booking::class, 1)->create([
                'client_id' => $this->client->id,
                'professional_id' => $professional->id,
                'service_id' => $professional->services->random()->id,
            ]))
        );

        $this->assertEquals(
            [
                'id' => "1498446000161400{$professional->id}",
                'date' => '2017-06-26',
                'interval' => '16:14:00',
                'professional' => $professional->makeHidden(['services', 'bookings'])->toArray(),
                'bookings' => $bookings->toArray(),
                'blocked' => false,
                'description' => 'Available',
                'available_services' => $professional->services->pluck('id')->toArray(),
            ],
            $calendarItem->jsonSerialize()
        );
    }
}
