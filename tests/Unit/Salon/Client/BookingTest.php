<?php

namespace Tests\Unit\Salon\Client;

use Carbon\Carbon;
use Tests\TestCase;
use App\Salon\Client\Booking;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Test Salon Booking
 */
class BookingTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_booking_can_not_be_instanciated_as_canceled() : void
    {
        tap(new Booking(['canceled_at' => Carbon::now()]), function ($booking) {
            $this->assertNull($booking->canceled_at);
        });
    }

    /** @test **/
    public function a_booking_is_created_on_a_date() : void
    {
        $booking = factory(Booking::class)->create([
            'date' => with($date = date_random()),
        ]);

        tap($booking, function ($booking) use ($date) {
            $this->assertTrue($booking->isOnDate($date));
            $this->assertFalse($booking->isOnDate($date->copy()->subSecond()));
            $this->assertFalse($booking->isOnDate($date->copy()->subMinute()));
            $this->assertFalse($booking->isOnDate($date->copy()->subHour()));
            $this->assertFalse($booking->isOnDate($date->copy()->subDay()));
        });

        tap($booking, function ($booking) use ($date) {
            $this->assertTrue($booking->isOnDate($date));
            $this->assertTrue($booking->isOnDate($date->copy()->addSecond()));
            $this->assertTrue($booking->isOnDate($date->copy()->addMinute()));
            $this->assertTrue($booking->isOnDate($date->copy()->addHour()));
            $this->assertFalse($booking->isOnDate($date->copy()->addDay()));
        });
    }

    /** @test **/
    public function a_booking_is_created_on_a_calendar_interval() : void
    {
        $booking = factory(Booking::class)->create([
            'start' => $time = time_random(),
        ]);

        tap($booking, function ($booking) use ($time) {
            $this->assertTrue($booking->isOnInterval($time));
            $this->assertFalse($booking->isOnInterval(Carbon::parse($time)->subSecond()->toTimeString()));
            $this->assertFalse($booking->isOnInterval(Carbon::parse($time)->subMinute()->toTimeString()));
            $this->assertFalse($booking->isOnInterval(Carbon::parse($time)->subHour()->toTimeString()));
        });

        tap($booking, function ($booking) use ($time) {
            $this->assertTrue($booking->isOnInterval($time));
            $this->assertFalse($booking->isOnInterval(Carbon::parse($time)->addSecond()->toTimeString()));
            $this->assertFalse($booking->isOnInterval(Carbon::parse($time)->addMinute()->toTimeString()));
            $this->assertFalse($booking->isOnInterval(Carbon::parse($time)->addHour()->toTimeString()));
        });
    }

    /** @test */
    public function a_booking_is_canceled() : void
    {
        $booking = factory(Booking::class)->create();

        tap($booking, function ($booking) {
            $this->assertNull($booking->canceled_at);
            $this->assertFalse($booking->isCanceled());

            $this->assertDatabaseHas(
                'client_bookings',
                array_merge($booking->toArray(), ['canceled_at' => null])
            );
        });

        $booking->cancel();

        tap($booking, function ($booking) {
            $this->assertEquals(Carbon::now(), $booking->canceled_at);
            $this->assertTrue($booking->isCanceled());

            $this->assertDatabaseHas(
                'client_bookings',
                array_merge($booking->toArray(), ['canceled_at' => Carbon::now()])
            );
        });
    }
}
