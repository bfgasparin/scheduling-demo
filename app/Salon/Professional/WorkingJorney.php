<?php

namespace App\Salon\Professional;

use Carbon\Carbon;
use App\Salon\Employee;
use App\Salon\Client\Booking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Salon\Professional\WorkingJorney\{Schedule, Absence};
use App\Salon\Professional\WorkingJorney\Concerns\HasCalendarAttributes;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasManyThrough};
use App\Eloquent\Concerns\Auth\ScopedByAuthUserSalonThroughProfessional;

/**
 * The Working Jorney of a Professional Employee
 *
 * Working Jorney data merged with The Working Jorney Schedules
 * describes a detailed Working Jorney of a professional.
 *
 * @see App\Salon\Employee
 * @see App\Salon\Professional\WorkingJorney\Schedules
 */
class WorkingJorney extends Model
{
    use HasCalendarAttributes,
        ScopedByAuthUserSalonThroughProfessional;

    protected $table = 'professional_working_jorneys';

    protected $fillable = ['entry', 'lunch', 'exit', 'days_of_week', 'calendar_interval'];

    protected $casts = [
        'days_of_week' => 'array',
        'calendar_interval' => 'integer',
        'entry' => 'string',
        'lunch' => 'string',
        'exit' => 'string',
    ];

    /**
     * The Professional this WorkingJorney is owned
     */
    public function professional() : BelongsTo
    {
        return $this->belongsTo(Employee::class)->professional();
    }

    /**
     * The custom schedules of the profesional WorkingJorney
     */
    public function schedules() : HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * The bookings related to the professional that owns this WorkingJorney
     */
    public function bookings() : HasMany
    {
        return $this->professional->bookings();
    }

    /**
     * The absences from work of the professional
     */
    public function absences() : HasMany
    {
        return $this->hasMany(Absence::class);
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new WorkingJorneyCollection($models);
    }
}
