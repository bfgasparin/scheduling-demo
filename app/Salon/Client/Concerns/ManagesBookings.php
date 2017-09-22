<?php

namespace App\Salon\Client\Concerns;

use App\Salon\Service;
use App\Salon\Employee;
use App\Salon\Client\Booking;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Contains methods to help the client to manage its bookings
 */
trait ManagesBookings
{
    /**
     * The bookings of the client
     *
     * @see App\Salon\Client\Booking
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings() : HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Create a new booking for this client using the given data
     *
     * @param array $data
     *
     * @return App\Salon\Client\Booking
     */
    public function createBooking(array $data) : Booking
    {
        return $this->bookings()->create($data);
    }
}
