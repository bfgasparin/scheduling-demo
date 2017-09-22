<?php

namespace App\Policies\Salon\Client;

use App\Auth\Authenticatable;
use App\Policies\Concerns\HasSalonWorkerPolicies;
use App\Salon\{Client\Booking, Client\User as ClientUser};

/**
 * Policies for Booking model
 */
class BookingPolicy
{
    use HasSalonWorkerPolicies;

    /**
     * Determine whether the user can booking a service
     *
     * @param  \App\Auth\Authenticatable  $user
     * @return mixed
     */
    public function create(Authenticatable $user)
    {
        if (is_a($user, ClientUser::class)) {
            return true;
        }

        return $this->check($user, function ($user) {
            // Any salon worker can create booking
            return true;
        });
    }

    /**
     * Determine whether the user can cancel a booking
     *
     * @param  \App\Auth\Authenticatable  $user
     * @param  \App\Salon\Client\Booking  $booking
     * @return mixed
     */
    public function cancel(Authenticatable $user, Booking $booking)
    {
        if (is_a($user, ClientUser::class) && $user->represents($booking->client)) {
            return true;
        }

        return $this->check($user, function ($user) {
            // Any salon worker can cancel booking
            return true;
        });
    }

    /**
     * Determine whether the user can view a booking
     *
     * @param  \App\Auth\Authenticatable  $user
     * @param  \App\Salon\Client\Booking  $booking
     * @return mixed
     */
    public function view(Authenticatable $user, Booking $booking)
    {
        if (is_a($user, ClientUser::class) && $user->represents($booking->client)) {
            return true;
        }

        return $this->check($user, function ($user) {
            // Any salon worker can view a booking
            return true;
        });
    }
}
