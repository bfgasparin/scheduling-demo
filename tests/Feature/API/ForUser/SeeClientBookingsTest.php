<?php

namespace Tests\Feature\API\ForUser;

use App\Salon;
use Tests\TestCase;
use App\Salon\Client;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\{SalonWorkerHelpers, SalonClientBookingHelpers};

/**
 * Testing a user seeing client Bookings
 *
 * @see App\User
 * @see App\Salon\Client\Booking
 * @see App\Salon\Service
 */
class SeeClientBookingsTest extends TestCase
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
    public function a_user_can_list_all_the_bookings_of_all_salons_where_it_is_client_of()
    {
        $bookings = Collection::times(20, function () {
            return $this->createBookingFor($this->authUser, $this->salon());
        });

        $this->actingAs($this->authUser, $this->authGuard)
            ->json('GET', "/api/bookings")
            ->assertSuccessful()
            ->assertJsonPagination(
                $bookings->forPage(1,15)->toArray(),
                20
            );
    }

    /** @test */
    public function a_user_can_not_list_bookings_from_another_client_of_the_salon()
    {
        // fixtures
        repeat(5, function () {
            return $this->createBookingOfAnotherClientOf($this->salon);
        });

        $bookings = Collection::times(10, function () {
            return $this->createBookingFor($this->client->user);
        });

        $this->actingAs($this->authUser, $this->authGuard)
            ->json('GET', "/api/salons/{$this->salon->id}/bookings")
            ->assertSuccessful()
            ->assertJsonPagination(
                $bookings->forPage(1,15)->toArray(),
                10
            );
    }

    /** @test */
    public function a_user_can_not_list_the_bookings_using_client_bookings_route()
    {
        $this->actingAs($this->authUser, $this->authGuard)
            ->json('GET', "/api/clientBookings")
            ->assertStatus(401);
    }

    /** @test */
    public function a_user_can_not_list_bookings_of_another_clients()
    {
        $bookings = Collection::times(10, function () {
            return $this->createBookingFor($this->authUser, $this->salon);
        });

        repeat(5, function () {
            $this->createBookingOfAnotherClientOf($this->salon);
            $this->createBookingOfAnotherSalon();
        });

        $this->actingAs($this->authUser, $this->authGuard)
            ->json('GET', "/api/bookings")
            ->assertSuccessful()
            ->assertJsonPagination(
                $bookings->forPage(1,15)->toArray(),
                10
            );
    }

    /** @test */
    public function a_user_can_see_a_booking_that_belongs_to_it()
    {
        $booking = $this->createBookingFor($this->client->user);

        $this->actingAs($this->authUser, $this->authGuard)
            ->json('GET', "/api/salons/{$this->salon->id}/bookings/{$booking->id}")
            ->assertStatus(200)
            ->assertJson($booking->toArray());
    }

    /** @test */
    public function a_user_can_not_see_a_booking_of_another_client_of_the_salon()
    {
        $booking = $this->createBookingOfAnotherClientOf($this->salon);

        $this->actingAs($this->authUser, $this->authGuard)
            ->json('GET', "/api/salons/{$this->salon->id}/bookings/{$booking->id}")
            ->assertStatus(404);
    }

    /** @test */
    public function a_user_can_not_see_a_booking_of_another_user()
    {
        $booking = $this->createBookingOfAnotherSalon();

        $this->actingAs($this->authUser, $this->authGuard)
            ->json('GET', "/api/salons/{$booking->client->salon_id}/bookings/{$booking->id}")
            ->assertStatus(404);
    }
}
