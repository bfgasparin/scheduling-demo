<?php

namespace App\Salon\Client\Booking\Concerns;

use Carbon\Carbon;
use App\Salon\Service;
use App\Eloquent\Concerns\HasDateFilters;
use Illuminate\Database\Eloquent\Builder;

/**
 * Adds some helpful calendar methods to manage calenter attributes on Booking model
 */
trait HasCalendarAttributes
{
    use HasDateFilters;

    /**
     * Return if this booking is registered on the given calendar interval
     *
     * @param string $interval
     *
     * @return bool
     */
    public function isOnInterval(string $interval) : bool
    {
        return Carbon::parse($this->start)->equalTo(Carbon::parse($interval));
    }

    /**
     * Return if this booking is of the given service
     *
     * @param Service $service
     *
     * @return bool
     */
    public function isOfService(Service $service) : bool
    {
        return $this->service->is($service);
    }
}
