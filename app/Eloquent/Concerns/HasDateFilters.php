<?php

namespace App\Eloquent\Concerns;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Add filters on Eloquent Models related to date instances
 */
trait HasDateFilters
{
    /**
     * Scope a query to only include models on the given date.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param mixed $date
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnDate(Builder $query, $date)
    {
        return $query->whereDate('date', Carbon::parse($date)->startOfDay());
    }

    /**
     * Scope a query to only include models on any date in the range of the given
     * date and the endDate
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param mixed $date
     * @param mixed $endDate
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnPeriod(Builder $query, $date, $endDate)
    {
        return $query->whereDate('date', '>=', Carbon::parse($date)->startOfDay())
            ->whereDate('date', '<=', Carbon::parse($endDate)->startOfDay());
    }

    /**
     * Returns if this model is registered on the given date
     *
     * @param mixed $date
     *
     * @return bool
     */
    public function isOnDate($date) : bool
    {
        return $this->date->equalTo(Carbon::parse($date)->startOfDay());
    }
}
