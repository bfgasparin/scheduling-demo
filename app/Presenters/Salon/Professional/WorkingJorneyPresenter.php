<?php

namespace App\Presenters\Salon\Professional;

use Carbon\Carbon;
use App\Salon\Calendar;
use Illuminate\Support\Collection;
use App\Salon\Professional\WorkingJorney;
use App\Salon\Calendar\Item as CalendarItem;
use App\Salon\Professional\WorkingJorneyCollection;

/**
 * Presenter for WorkingJorney model.
 *
 * Helps to present WorkingJorney models and its relations to a presentable data
 * for use on view layer
 */
class WorkingJorneyPresenter
{
    /** @var App\Eloquent\Collections\Salon\Professional\WorkingJorneyCollection */
    protected $workingJorneys;

    /**
     * Creates a new instance of the presenter
     *
     * @param mixed $workingJorney The workingJorneys to be presented
     */
    public function __construct($workingJorney)
    {
        // Ensure that the workingJorneys attribute is a WorkingJorneyCollection
        $this->workingJorneys = ! is_a($workingJorney, WorkingJorneyCollection::class)
            ? $workingJorney->newCollection([$workingJorney])
            : $workingJorney;
    }

    /**
     * Converts the Collection of WorkingJorney into a presentable Calendar Collection
     *
     * The returned Calendar Collection contains a range of Calendar Items
     * construct using the professional calendar intervals of each professional found
     * in the WorkingJorney collection
     * @see App\Salon\Professional\WorkingJorney::calendar_interval_range
     *
     * @return App\Salon\Calendar
     */
    public function calendar($date, $endDate = null) : Calendar
    {
        [$begin, $end] = $this->parseToCarbon($date, $endDate ?? $date);

        return new Calendar(
            $this->createCalendarItemsForRange($begin, $end)
        );
    }

    /**
     * Create a collection of presentable CalendarItem for the given range of dates
     *
     * @param mixed $date
     * @param WorkingJorney $workingJorney
     *
     * @return Illuminate\Support\Collection
     */
    protected function createCalendarItemsForRange($begin, $end) : Collection
    {
        return collect(date_range($begin, $end))->flatMap(function ($date) {
            return $this->workingJorneys->forDate($date)->flatMap(function ($workingJorney) use ($date) {
                return $this->createCalendarItemsForDate($date, $workingJorney);
            });
        });
    }

    /**
     * Create a collection of presentable CalendarItem for the given date using the given
     * WorkingJorney
     *
     * @param mixed $date
     * @param WorkingJorney $workingJorney
     *
     * @return Illuminate\Support\Collection
     */
    protected function createCalendarItemsForDate($date, WorkingJorney $workingJorney) : Collection
    {
        return $workingJorney->getCalendarIntervalRangeOn($date)
            ->map(function ($interval) use ($date, $workingJorney) {
                return new CalendarItem(
                    $date,
                    $interval,
                    $workingJorney->professional,
                    $workingJorney->bookings->onDate($date)->onInterval($interval)->values()
                );
            });
    }

    /**
     * Parse the given dates to Carbon instances and return the parsed dates
     *
     * @param ... $dates
     *
     * @return array The parsed dates
     */
    protected function parseToCarbon(...$dates) : array
    {
        return collect($dates)->map(function ($date) {
            return Carbon::parse($date);
        })->toArray();
    }
}
