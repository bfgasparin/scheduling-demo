<?php

namespace App\Salon\Professional\WorkingJorney\Absence\Concerns;

use App\Eloquent\Concerns\HasDateFilters;

/**
 * Adds some helpful calendar methods to manage calenter attributes on Absence model
 */
trait HasCalendarAttributes
{
    use HasDateFilters;
}
