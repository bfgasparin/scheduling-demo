<?php

namespace App\Salon\Client;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * Collection for Booking models
 */
class BookingCollection extends EloquentCollection
{
    /**
     * Filter all bookings that is registered for the given date
     *
     * @param mixed $date
     *
     * @return self
     */
    public function onDate($date) : self
    {
        return $this->filter->isOnDate($date);
    }

    /**
     * Filter all bookings that is registered for the given calendar inverval
     *
     * @param string $interval
     *
     * @return self
     */
    public function onInterval(string $interval) : self
    {
        return $this->filter->isOnInterval($interval);
    }

    /**
     * Filter all bookings of the given service
     *
     * @param Service $service
     *
     * @return self
     */
    public function ofService(Service $service) : self
    {
        return $this->filter->isOfService($service);
    }
}
