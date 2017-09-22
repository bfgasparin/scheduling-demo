<?php

namespace App\Salon\Employee\Concerns;

use App\Salon\Client;
use App\Salon\Service;
use App\Salon\Client\Booking;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Exceptions\Salon\Client\Booking\{
    Exception as BookingException,
    ServiceNotOfferedByProfessional,
    CalendarIntervalFull
};

/**
 * Contains methods to help the professional to manage client bookings
 */
trait ManageClientBookings
{
    /**
     * The bookings associates to the professional
     */
    public function bookings() : HasMany
    {
        return $this->hasMany(Booking::class, 'professional_id');
    }

    /**
     * Return if a booking with the given date, interval and service can be created for this professional
     *
     * @param mixed $date
     * @param string $interval
     * @param App\Salon\Service $service
     *
     * @return bool
     */
    public function canHaveNewBookingWith($date, string $interval, Service $service) : bool
    {
        try {
            $this->validateNewBookingWith($date, $interval, $service);
        } catch (BookingException $e) {
            return false;
        }

        return true;
    }

    /**
     * Check if a booking with the given date, interval and service can be created for the professional
     *
     * Throw a Booking Exception if the check fails
     *
     * @param mixed $date
     * @param string $interval
     * @param App\Salon\Service $service
     *
     * @return void
     *
     * @throw App\Exceptions\Salon\Client\Booking\Exception
     */
    public function validateNewBookingWith($date, string $interval, Service $service) : void
    {
        // Deny if the professional does not offer the servive
        if (! $this->offersService($service)) {
            throw new ServiceNotOfferedByProfessional($this, $service);
        }

        // Deny if there is a booking already registered on the given date and interval
        if ($this->bookings->onDate($date)->onInterval($interval)->isNotEmpty()) {
            throw new CalendarIntervalFull($date, $interval, $this, $service);
        }
    }
}
