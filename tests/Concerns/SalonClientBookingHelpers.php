<?php

namespace Tests\Concerns;

use App\User;
use App\Salon;
use Carbon\Carbon;
use App\Salon\Client;
use App\Salon\Client\Booking;
use App\Salon\Client\User as ClientUser;
use App\Salon\Config\Booking as ConfigBooking;

/**
 * Contains helper functions for tests using a Salon Client Booking
 *
 * @see App\Salon\Client\Booking
 */
trait SalonClientBookingHelpers
{
    /**
     * The salon to be used on tests
     * @var Salon
     */
    protected $salon;

    /** @before */
    protected function setUpSalon() : void
    {
        $this->salon = tap($this->salon(), function ($salon) {
            $minutesToday =  Carbon::today()->diffInMinutes();

            factory(ConfigBooking::class)->create([
                'salon_id' => $salon->id,
                'create_tolerance_for_client_user' => with($minutesToday - rand(1, $minutesToday -1)),
                'cancel_tolerance_for_client_user' => with($minutesToday - rand(1, $minutesToday -1)),
            ]);
        });
    }

    /**
     * Returns a Salon to be used on test
     *
     * @return App\Salon
     */
    protected function salon() : Salon
    {
        return factory(Salon::class)->create();
    }

    /**
     * Return data booking using the given atributes
     *
     * @param array $attributes
     *
     * @return array
     */
    protected function bookingData(array $attributes = []) : array
    {
        return factory(Booking::class)->states('past_one_year')->make(
            $attributes + ['client_id' => $this->createClient()->id]
        )->toArray();
    }

    /**
     * Create a new client using the given attributes and the given salon
     *
     * @param array $attributes
     * @param App\Salon $salon
     *
     * @return App\Salon\Client
     */
    protected function createClient(array $attributes = [], Salon $salon = null) : Client
    {
        return factory(Client::class)->create(
            $attributes + ['salon_id' => $salon->id ?? $this->salon->id]
        );
    }

    /*
     * Create a new Client from another salon
     *
     * @return App\Salon\{Client\Booking
     */
    protected function createClientOfAnotherSalon() : Client
    {
        return $this->createClient([], factory(Salon::class)->create());
    }


    /**
     * Create a User (Salon Client User) with some ramdom clients to be used with a booking test
     *
     * @param ...$clients a Collection of clients to attach to the user, if wanted
     *
     * @return App\Salon\Client\User
     */
    protected function createUserWithClients(...$clients) : ClientUser
    {
        return tap(factory(User::class)->states('active')->create(), function ($user) use ($clients) {
            $user->clients()->saveMany(factory(Client::class, rand(1,5))->create()->merge($clients));
        });
    }

    /**
     * Create a new Booking using the given states
     *
     * @param ...$states
     *
     * @return App\Salon\Client\Booking
     */
    protected function createBooking(...$states) : Booking
    {
        return factory(Booking::class)->states($states)->create([
            'client_id' => factory(Client::class)->lazy(['salon_id' => $this->salon->id])
        ]);
    }

    /**
     * Create a random Booking for the given User (Client User) or the given Salon
     *
     * @param App\Salon\Client\User  $user
     * @param App\Salon  $salon
     *
     * @return App\Salon\Client\Booking
     */
    protected function createBookingFor(ClientUser $user, Salon $salon = null) : Booking
    {
        return factory(Booking::class)->create([
            'client_id' => factory(Client::class)->firstOrCreate(
                ['user_id' => $user->id, 'salon_id' => $salon->id ?? $this->salon->id]
            ),
        ]);
    }

    /**
     * Create a Booking for the given the user from a salon different from the
     * tested salon
     *
     * @param App\Salon\Client\User $user
     * @return Booking
     */
    protected function createBookingOfAnotherSalonFor(ClientUser $user) : Booking
    {
        return $this->createBookingFor($user, factory(Salon::class)->create());
    }

    /**
     * Create a Booking of Another Client of the given salonSalon
     *
     * @param Salon $salon
     * @return Booking
     */
    protected function createBookingOfAnotherClientOf(Salon $salon) : Booking
    {
        return $this->createBookingFor($this->createUserWithClients(), $salon);
    }

    /**
     * Create a booking for another cient user
     *
     * @return Booking
     */
    protected function createBookingOfAnotherSalon() : Booking
    {
        return factory(Booking::class)->create();
    }
}
