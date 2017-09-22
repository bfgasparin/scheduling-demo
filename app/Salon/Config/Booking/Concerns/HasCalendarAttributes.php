<?php

namespace App\Salon\Config\Booking\Concerns;

use Illuminate\Support\Collection;

/**
 * Adds some helpful calendar methods to manage calenter attributes on Salon Config Booking model
 */
trait HasCalendarAttributes
{
    /**
     * Returns the range of calendar invervals on a given date for owned salon
     *
     * @param mixed $date
     *
     * @return Illuminate\Support\Collection
     */
    public function getCalendarIntervalRangeOn($date) : Collection
    {
        $workingJorneys = $this->salon->workingJorneys;

        return collect(time_range(
            $workingJorneys->earlyEntryOn($date),
            $workingJorneys->laterExitOn($date),
            $this->calendar_interval
        ));
    }
}
