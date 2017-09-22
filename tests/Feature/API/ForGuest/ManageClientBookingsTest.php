<?php

namespace Tests\Feature\API\ForGuest;

use Tests\TestCase;
use App\Salon\Client\Booking;
use Tests\Concerns\SalonClientBookingHelpers;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Testing a non authenticated user managing client bookings
 */
class ManageClientBookingsTest extends TestCase
{
    use DatabaseTransactions,
        SalonClientBookingHelpers;

    /** @test */
    public function a_guest_can_not_create_a_booking()
    {
        $data = $this->bookingData();

        $this->json('POST', "/api/salons/{$this->salon->id}/bookings", $data)
            ->assertStatus(401);

        $this->assertDatabaseMissing('client_bookings', $data);
    }

    /** @test */
    public function a_guest_can_not_see_a_booking()
    {
        $booking = $this->createBooking();

        $this->json('GET', "/api/salons/{$this->salon->id}/bookings/{$booking->id}")
            ->assertStatus(401);

        $this->json('GET', "/api/clients/{$booking->client_id}/bookings/{$booking->id}")
            ->assertStatus(401);
    }

    /** @test */
    public function a_guest_can_not_list_bookings()
    {
        $booking = $this->createBooking();

        $this->json('GET', "/api/bookings")
            ->assertStatus(401);

        $this->json('GET', "/api/clientBookings")
            ->assertStatus(401);

        $this->json('GET', "/api/clients/{$booking->client_id}/bookings")
            ->assertStatus(401);
    }

    // TODO test others urls for bookings
}
