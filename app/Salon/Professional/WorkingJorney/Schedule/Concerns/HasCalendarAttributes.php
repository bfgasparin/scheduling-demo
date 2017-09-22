<?php

namespace App\Salon\Professional\WorkingJorney\Schedule\Concerns;

use App\Eloquent\Concerns\HasDateFilters;

/**
 * Adds some helpful calendar methods to manage calenter attributes on Schedule model
 */
trait HasCalendarAttributes
{
    use HasDateFilters;

    /**
     * Returns the day of the wrrj of the schedule
     *
     * @return int
     */
    public function getDayOfWeekAttribute() : int
    {
        return $this->date->dayOfWeek;
    }
}
