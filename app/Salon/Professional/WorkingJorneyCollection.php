<?php

namespace App\Salon\Professional;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Presenters\Salon\Professional\WorkingJorneyPresenter;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * Collection for WorkingJorney models
 */
class WorkingJorneyCollection extends EloquentCollection
{
    /**
     * Convert this collection into a presentable Calendar collection
     * @see App\Presenters\Salon\Professional\WorkingJorneyPresenter
     *
     * @return Illuminate\Support\Collection
     */
    public function toCalendar() : Collection
    {
        $earlyDate = $this->min(function ($workingJorney) {
            return $workingJorney->bookings ? $workingJorney->bookings->min('date') : null;
        });
        $laterDate = $this->max(function ($workingJorney) {
            return $workingJorney->bookings ? $workingJorney->bookings->max('date') : null;
        });

        return (new WorkingJorneyPresenter($this))->calendar($earlyDate, $laterDate);
    }

    /**
     * Filters all workingJorneys that represents the given date
     *
     * @param mixed $date
     *
     * @return self;
     */
    public function forDate($date) : self
    {
        return $this->filter->representsDate($date);
    }

    /**
     * Returns the early entry on the given date among all workingJorneys in the Collection
     *
     * @param mixed $date
     *
     * @return string|Null
     */
    public function earlyEntryOn($date) : ?string
    {
        return time_min(...$this->map->getEntryOn($date));
    }

    /**
     * Returns the later exit on the given date among all workingJorneys in the Collection
     *
     * @return string|Null
     */
    public function laterExitOn($date) : ?string
    {
        return time_max(...$this->map->getExitOn($date));
    }
}
