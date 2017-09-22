<?php

namespace Tests\Feature\API\ForUser;

use Bus;
use App\Salon;
use Tests\TestCase;
use App\Salon\Client;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\{SalonWorkerHelpers, SalonClientBookingHelpers};
use App\Jobs\Salon\Client\Booking\CancelThroughUser as CancelBookingThroughUser;

/**
 * Testing a user canceling client Bookings
 *
 * @see App\User
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
    protected $authGuard = 'api-users';

    /** @before */
    public function setUpClient() : void
    {
        $this->client = factory(Client::class)->create();
    }

    /** @before */
    public function setUpAuthUser() : void
    {
        $this->authUser = $this->createUserWithClients($this->client);
    }

    /**
     * Returns a Salon to be used on Tests
     * @see Tests\Concerns\SalonClientBookingHelpers
     *
     * @return App\Salon
     */
    protected function salon() : Salon
    {
        return $this->client->salon;
    }

    /** @test */
    public function a_user_can_cancel_a_booking()
    {
        Bus::fake();

        $booking = $this->createBookingFor($this->client->user);

        $this->actingAs($this->authUser, $this->authGuard)
            ->json('DELETE', "/api/salons/{$this->salon->id}/bookings/{$booking->id}")
            ->assertStatus(202);

        Bus::assertDispatched(CancelBookingThroughUser::class, function ($job) use ($booking) {
            return $job->user->is($this->authUser) && $job->booking->is($booking);
        });
    }

    /** @test */
    public function a_user_can_not_cancel_a_booking_of_a_salon_using_another_salon()
    {
        Bus::fake();

        $booking = $this->createBookingOfAnotherSalonFor($this->client->user);

        $this->actingAs($this->authUser, $this->authGuard)
            ->json('DELETE', "/api/salons/{$this->salon->id}/bookings/{$booking->id}")
            ->assertStatus(404);

        Bus::assertNotDispatched(CancelBookingThroughUser::class);
    }

    /** @test */
    public function a_user_can_not_cancel_a_booking_of_another_client_of_the_salon()
    {
        Bus::fake();

        $booking = $this->createBookingOfAnotherClientOf($this->salon);

        $this->actingAs($this->authUser, $this->authGuard)
            ->json('DELETE', "/api/salons/{$this->salon->id}/bookings/{$booking->id}")
            ->assertStatus(404);

        Bus::assertNotDispatched(CancelBookingThroughUser::class);
    }
}
