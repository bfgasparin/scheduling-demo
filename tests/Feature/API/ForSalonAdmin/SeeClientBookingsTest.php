<?php

namespace Tests\Feature\API\ForSalonAdmin;

use App\Salon;
use Tests\TestCase;
use App\Salon\Client;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\{SalonWorkerHelpers, SalonClientBookingHelpers};

/**
 * Testing an Admin seeing bookings of its clients
 *
 * @see App\Salon\Client
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
     * @see Tests\Feature\API\Concerns\SalonClientBookingHelpers
     *
     * @return App\Salon
     */
    public function salon() : Salon
    {
        return $this->client->salon;
    }

    /** @test */
    public function a_salon_admin_can_list_bookings_on_the_salon()
    {
        $bookings = Collection::times(10, function () {
            return $this->createBooking();
        });

        $this->actingAs($this->authUser, $this->authGuard)
            ->json('GET', "/api/clientBookings")
            ->assertSuccessful()
            ->assertJsonPagination(
                $bookings->forPage(1,15)->toArray(),
                10
            );
    }

    /** @test */
    public function a_salon_admin_can_list_only_bookings_of_salon_it_belongs()
    {
        $bookings = Collection::times(10, function () {
            return $this->createBooking();
        });

        repeat(5, function () {
            $this->createBookingOfAnotherSalon();
        });

        $this->actingAs($this->authUser, $this->authGuard)
            ->json('GET', "/api/clientBookings")
            ->assertSuccessful()
            ->assertJsonPagination(
                $bookings->forPage(1,15)->toArray(),
                10
            );
    }

    /** @test */
    public function a_salon_admin_can_list_bookings_of_a_client()
    {
        $bookings = Collection::times(10, function () {
            return $this->createBookingFor($this->client->user);
        });

        $this->actingAs($this->authUser, $this->authGuard)
            ->json('GET', "api/clients/{$this->client->id}/bookings")
            ->assertSuccessful()
            ->assertJsonPagination(
                $bookings->forPage(1,15)->toArray(),
                10
            );
    }

    /** @test */
    public function a_salon_admin_can_not_list_bookings_of_client_of_another_salon()
    {
        // fixtures
        $client = $this->createClientOfAnotherSalon();

        $this->actingAs($this->authUser, $this->authGuard)
            ->json('GET', "api/clients/{$client->id}/bookings")
            ->assertStatus(404);
    }

    /** @test */
    public function a_salon_admin_can_not_list_bookings_using_bookings_route()
    {
        $this->actingAs($this->authUser, $this->authGuard)
            ->json('GET', "/api/bookings")
            ->assertStatus(401);
    }

    /** @test */
    public function a_salon_admin_can_not_list_bookings_using_salons_route()
    {
        $this->actingAs($this->authUser, $this->authGuard)
            ->json('GET', "/api/salons/{$this->authUser->salon_id}/bookings")
            ->assertStatus(401);
    }

    /** @test */
    public function a_salon_admin_can_see_a_booking_that_belongs_to_client()
    {
        $booking = $this->createBookingFor($this->client->user);

        $this->actingAs($this->authUser, $this->authGuard)
            ->json('GET', "/api/clients/{$this->client->id}/bookings/{$booking->id}")
            ->assertStatus(200)
            ->assertJson($booking->toArray());
    }

    /** @test */
    public function a_salon_admin_can_not_see_a_booking_of_another_salon()
    {
        $booking = $this->createBookingOfAnotherSalon();

        $this->actingAs($this->authUser, $this->authGuard)
            ->json('GET', "/api/clients/{$booking->client_id}/bookings/{$booking->id}")
            ->assertStatus(404);
    }

    /** @test */
    public function a_salon_admin_can_not_see_a_booking_that_belongs_to_its_client_but_on_another_salon()
    {
        $booking = $this->createBookingOfAnotherSalonFor($this->client->user);

        $this->actingAs($this->authUser, $this->authGuard)
            ->json('GET', "/api/clients/{$booking->client_id}/bookings/{$booking->id}")
            ->assertStatus(404);
    }

    /** @test */
    public function a_salon_admin_can_not_see_a_booking_using_salons_route()
    {
        $booking = $this->createBookingFor($this->client->user);

        $this->actingAs($this->authUser, $this->authGuard)
            ->json('GET', "/api/salons/{$this->authUser->salon_id}/bookings/{$booking->id}")
            ->assertStatus(401);
    }

}
