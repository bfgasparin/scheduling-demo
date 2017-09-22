<?php

namespace App\Queries\Salon;

use BadMethodCallException;
use App\Salon\Professional\WorkingJorney;
use Illuminate\Database\Eloquent\Builder;
use App\Salon\{Calendar, EmployeeCollection};
use App\Salon\Professional\WorkingJorneyCollection;
use App\Presenters\Salon\Professional\WorkingJorneyPresenter;

/**
 * Query Working jorney models then convert the the result to presentable Calendar Collection
 * @see App\Salon\Calendar
 * @see App\Presenters\Salon\Professional\WorkingJorneyPresenter::calendar
 * @see App\Eloquent\Collections\Salon\Professional\WorkingJorneyCollection
 *
 * A date must be used to filter the data.
 * @see self::get
 */
class CalendarQuery
{
    /** @var App\Salon\Employee */
    protected $professionals;

    /**
     * Scope the query to include only data related to the given professional/professionals
     *
     * @param mixed $professional
     */
    public function scopeForProfessional($professional)
    {
        // Ensure that the professionals attribute is a ProfessionalCollection
        $this->professionals = ! is_a($professional, EmployeeCollection::class)
            ? $professional->newCollection([$professional])
            : $professional;
    }

    /**
     * Execute the query
     *
     * @param mixed $date
     * @param mixed $endDate
     *
     * @return App\Salon\Calendar
     */
    public function get($date, $endDate = null) : Calendar
    {
        return $this->getWorkingJorneysOn($date, $endDate)->toCalendar();
    }

    /**
     * Get a collection of WorkingJorney and its relations, filtering by the given
     * date and endDate
     *
     * @param mixed $date
     * @param mixed $endDate
     *
     * @return WorkingJorneyCollection
     */
    protected function getWorkingJorneysOn($date, $endDate = null) : WorkingJorneyCollection
    {
        return tap(WorkingJorney::query(), function ($query) use ($date, $endDate) {
            $this->applyProfessionalScope($query)
                ->applyDateScope($query, $date, $endDate)
                ->applyDateScopeOnRelations($query, $date, $endDate);
        })->get();
    }

    /**
     * Scope the query to include only WorkingJorneys for the given professional/professionals
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     *
     * @return self
     */
    protected function applyProfessionalScope(Builder $query) : self
    {
        $query->when($this->professionals, function ($query) {
            // If professional was set, scope the query to include only WorkingJorney
            // that belongs to the these professionals.
            //
            // Disable byAuthUserSalonThroughProfessional global scope to not duplicate
            // the golbal scope query with the following one
            return $query->withoutGlobalScope('byAuthUserSalonThroughProfessional')
                ->whereHas('professional', function ($query) {
                    $query->whereIn('id', $this->professionals->pluck('id'));
                });
        });

        return $this;
    }

    /**
     * Scope the query to only include WorkingJorneys that represents the given date.
     *
     * If the endDate is given, scopes to include WorkingJorneys that represents any
     * dates in the range of the date and endDate
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param mixed $date
     * @param mixed $endDate
     *
     * @return self
     */
    protected function applyDateScope(Builder $query, $date, $endDate = null) : self
    {
        $query->forDate($date, $endDate);

        return $this;
    }

    /**
     * Scope the query to only include WorkingJorneys relations on the given date.
     *
     * If the endDate is given, scopes to only include WorkingJorneys relations on
     * any dates in the range of the date and endDate
     *
     * The relations scoped are:
     * - bookings
     * - schedules
     * - absences
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param mixed $date
     * @param mixed $endDate
     *
     * @return self
     */
    protected function applyDateScopeOnRelations(Builder $query, $date, $endDate = null) : self
    {
        $query->when($endDate, function ($query) use ($date, $endDate) {
            return $query->with([
                'professional.bookings' => function ($query) use ($date, $endDate) {
                    $query->onPeriod($date, $endDate);
                },
                'schedules' => function ($query) use ($date, $endDate) {
                    $query->onPeriod($date, $endDate);
                },
                'absences' => function ($query) use ($date, $endDate) {
                    $query->onPeriod($date, $endDate);
                }
            ]);
        }, function ($query) use ($date) {
            return $query->with([
                'professional.bookings' => function ($query) use ($date) {
                    $query->onDate($date);
                },
                'schedules' => function ($query) use ($date) {
                    $query->onDate($date);
                },
                'absences' => function ($query) use ($date) {
                    $query->onDate($date);
                }
            ]);
        });

        return $this;
    }

    /**
     * Execute the query without any scope
     *
     * @param  dynamic  date|date,endDate
     *
     * @return WorkingJorneyCollection
     */
    public static function all() : WorkingJorneyCollection
    {
        return (new static)->get(...func_get_args());
    }

    /**
     * Handle dynamic method calls into the query instance
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (method_exists($this, $scope = 'scope'.ucfirst($method))) {
            return $this->$scope(...array_values($parameters)) ?: $this;
        }

        $className = static::class;

        throw new BadMethodCallException("Call to undefined method {$className}::{$method}()");

        return $this;
    }

    /**
     * Handle dynamic static method calls into the method.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }
}
