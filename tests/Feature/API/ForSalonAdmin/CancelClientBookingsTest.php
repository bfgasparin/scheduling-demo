<?php

namespace Tests\Feature\API\ForSalonAdmin;

use Bus;
use App\Salon;
use Tests\TestCase;
use App\Salon\Client;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Jobs\Salon\Client\Booking\Cancel as CancelBooking;
use Tests\Concerns\{SalonWorkerHelpers, SalonClientBookingHelpers};

/**
 * Testing an Admin canceling bookings of its clients
 *
 * @see App\Salon\Client
 * @see App\Salon\Client\Booking
 * @see App\Salon\Service
 */
class CancelClientBookingsTest extends TestCase
{
    use DatabaseTransactions,
        SalonWorkerHelpers,
        SalonClientBookingHelpers;

    /**
     * The client the authenticated user represents
     * @var App\Salon\Client
     */
    protected $client;

    /**
     * User authenticated which will test the feature
     *
     * @var App\Salon\Client\User
     */
    protected $authUser;

    /**
     * The authGuard used to login the user booking the service
     *
     * @var string
a    */
    protected $authGuard = 'api-salon-admins';

    /** @before */
    public function setUpClient() : void
    {
        $this->client = factory(Client::class)->create();
    }

    /** @before */
    public function setUpAuthUser() : void
    {
        $this->authUser = $this->createEmployeeAdmin(['salon_id' => $this->client->salon_id]);
    }

    /**
     * Returns a Salon to be used on test the Resource CRUDs
     * @see Tests\Feature\API\Concerns\SalonWorkerCRUDTests
     *
     * @return App\Salon
     */
    public function salon() : Salon
    {
        return $this->client->salon;
    }

    /** @test */
    public function a_salon_admin_can_cancel_a_booking()
    {
        Bus::fake();

        $booking = $this->createBookingFor($this->client->user);

        $this->actingAs($this->authUser, $this->authGuard)
            ->json('DELETE', "api/clients/{$booking->client_id}/bookings/{$booking->id}")
            ->assertStatus(202);

        Bus::assertDispatched(CancelBooking::class, function ($job) use ($booking) {
            return $job->booking->is($booking);
        });
    }

    /** @test */
    public function a_salon_admin_can_not_cancel_a_booking_that_belongs_to_its_client_but_on_another_salon()
    {
        Bus::fake();

        $booking1 = $this->createBookingOfAnotherSalonFor($this->client->user);
        $booking2 = $this->createBookingOfAnotherSalon();

        tap($booking1, function ($booking) {
            $this->actingAs($this->authUser, $this->authGuard)
                ->json('DELETE', "/api/clients/{$booking->client_id}/bookings/{$booking->id}")
                ->assertStatus(404);

            Bus::assertNotDispatched(CancelBooking::class);
        });

        tap($booking2, function ($booking) {
            $this->actingAs($this->authUser, $this->authGuard)
                ->json('DELETE', "/api/clients/{$booking->client_id}/bookings/{$booking->id}")
                ->assertStatus(404);

            Bus::assertNotDispatched(CancelBooking::class);
        });
    }

    /** @test */
    public function a_salon_admin_can_not_cancel_a_booking_using_salons_route()
    {
        Bus::fake();
        $booking1 = $this->createBookingFor($this->client->user);
        $booking2 =  $this->createBookingOfAnotherSalonFor($this->client->user);

        tap($booking1, function ($booking) {
            $this->actingAs($this->authUser, $this->authGuard)
                ->json('DELETE', "/api/salons/{$this->authUser->salon_id}/bookings/{$booking->id}")
                ->assertStatus(401);

            Bus::assertNotDispatched(CancelBooking::class);
        });

        tap($booking2, function ($booking) {
            $this->actingAs($this->authUser, $this->authGuard)
                ->json('DELETE', "/api/salons/{$booking->salon_id}/bookings/{$booking->id}")
                ->assertStatus(404);

            Bus::assertNotDispatched(CancelBooking::class);
        });
    }


}
