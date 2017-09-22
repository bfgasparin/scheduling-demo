<?php

namespace Tests\Integration\Salon\Client;

use Tests\TestCase;
use Tests\Concerns\SalonWorkerHelpers;
use App\Salon\{Client\Booking, Service};
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Test Integrations on the system when creating a booking instance
 */
class BookingTest extends TestCase
{
    use DatabaseTransactions,
        SalonWorkerHelpers;

    /** @test */
    public function a_copy_of_service_price_is_saved_on_booking_when_booking_is_created() : void
    {
        tap(factory(Booking::class)->create(), function ($booking) {
            $this->assertEquals($booking->service->price, $booking->service_price);
        });
    }

    /**
     * @test
     * @expectedException App\Exceptions\Salon\Client\Booking\UpdateNotAllowed
     */
    public function the_booking_service_can_not_be_changed_after_a_booking_is_created() : void
    {
        tap(factory(Booking::class)->create(), function ($booking) {
            $booking->service()->associate(factory(Service::class)->create(['salon_id' => $booking->client->salon_id]));
            $booking->save();
        });
    }

    /**
     * @test
     * @expectedException App\Exceptions\Salon\Client\Booking\UpdateNotAllowed
     */
    public function the_booking_professional_can_not_be_changed_after_a_booking_is_created() : void
    {
        tap(factory(Booking::class)->create(), function ($booking) {
            $professional = $this->createProfessional(['salon_id' => $booking->client->salon_id]);
            $booking->professional()->associate($professional);
            $booking->save();
        });
    }

    /**
     * @test
     * @expectedException App\Exceptions\Salon\Client\Booking\UpdateNotAllowed
     */
    public function the_booking_date_can_not_be_changed_after_a_booking_is_created() : void
    {
        tap(factory(Booking::class)->create(), function ($booking) {
            $booking->date = date_random();
            $booking->save();
        });
    }

    /**
     * @test
     * @expectedException App\Exceptions\Salon\Client\Booking\UpdateNotAllowed
     */
    public function the_booking_start_can_not_be_changed_after_a_booking_is_created() : void
    {
        tap(factory(Booking::class)->create(), function ($booking) {
            $booking->start = time_random();
            $booking->save();
        });
    }
}
