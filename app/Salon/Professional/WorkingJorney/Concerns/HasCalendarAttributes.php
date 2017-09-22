<?php

namespace App\Salon\Professional\WorkingJorney\Concerns;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

/**
 * Adds some helpful calendar methods to manage calenter attributes on WorkingJorney model
 */
trait HasCalendarAttributes
{
    /**
     * Scope the query to only include WorkingJorneys that represents
     * the given date.
     *
     * If the endDate is given, scopes to include WorkingJorneys that
     * represents any dates in the range of the date and endDate
     * @see self::scopeForPeriod
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param mixed $date
     * @param mixed $endDate
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDate(Builder $query, $date, $endDate = null)
    {
        $date = Carbon::parse($date);

        // filter for workingJorneys that represents the day of week from the given date
        // or from the the given range of dates
        return $query->when($endDate, function ($query) use ($date, $endDate) {
            return $query->forPeriod($date, $endDate);
        }, function ($query) use ($date) {
            return $query->whereRaw(
                "json_contains(`professional_working_jorneys`.`days_of_week`, '{$date->dayOfWeek}', '$')"
            );
        });
    }

    /**
     * Scopes the query to include WorkingJorneys that
     * represents any date in the range of the given date and the endDate
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param mixed $date
     * @param mixed $endDate
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeForPeriod(Builder $query, $date, $endDate) : Builder
    {
        // extract all unique days of week from the given range of dates
        $daysOfWeek = collect(date_range(Carbon::parse($date), Carbon::parse($endDate)))
            ->pluck('dayOfWeek')->unique();

        // ... and filter for workingJorneys that represents any day of week of the extracted days
        return $query->where(function ($query) use ($daysOfWeek) {
            $daysOfWeek->each(function ($dayOfWeek) use ($query) {
                $query->orWhereRaw(
                    "json_contains(`professional_working_jorneys`.`days_of_week`, '{$dayOfWeek}', '$')"
                );
            });
        });
    }

    /**
     * Return the range of calendar invervals on a given date of this WorkingJorney
     *
     * @param mixed $date
     *
     * @return Illuminate\Support\Collection
     */
    public function getCalendarIntervalRangeOn($date) : Collection
    {
        return collect(time_range(
            $this->getEntryOn($date),
            $this->getExitOn($date),
            $this->calendar_interval
        ));
    }

    /**
     * Return if the workingJorney represents the given date
     *
     * @param mixed $date
     *
     * @return bool
     */
    public function representsDate($date) : bool
    {
        return in_array(Carbon::parse($date)->dayOfWeek, $this->days_of_week);
    }

    /**
     * Return the entry on the given date of this WorkingJorney
     *
     * @param mixed $date
     *
     * @return string
     */
    public function getEntryOn($date) : string
    {
        if ($schedule = $this->schedules->first->isOnDate($date)) {
            return $schedule->entry;
        }

        return $this->entry;
    }

    /**
     * Return the exit on the given date of this WorkingJorney
     *
     * @param mixed $date
     *
     * @return string
     */
    public function getExitOn($date) : string
    {
        if ($schedule = $this->schedules->first->isOnDate($date)) {
            return $schedule->exit;
        }

        return $this->exit;
    }
}
