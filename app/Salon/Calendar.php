<?php

namespace App\Salon;

use Illuminate\Support\Collection;

/**
 * A Collection of CalendarItems
 *
 * Represents a Calendar in the system.
 */
class Calendar extends Collection
{
    /**
     * Filter the Calendar Items with the given prfessionals
     *
     * @return self
     */
    public function forProfessional(Employee $professional) : self
    {
        return $this->filter->hasProfessional($professional);
    }

    /**
     * Filters all Calendar Items for the given date
     *
     * @param mixed $date
     *
     * @return self
     */
    public function onDate($date) : self
    {
        return $this->filter->isOnDate($date);
    }
}
