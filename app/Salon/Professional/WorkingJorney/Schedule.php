<?php

namespace App\Salon\Professional\WorkingJorney;

use Illuminate\Database\Eloquent\Model;
use App\Salon\Professional\WorkingJorney;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Salon\Professional\WorkingJorney\Schedule\Concerns\HasCalendarAttributes;

/**
 * An schedule is a date specific Professional`s Working Jorney
 *
 * It defines the professional working jorney
 * for an especific date
 *
 * @see App\Salon\Professional\WorkingJorney
 */
class Schedule extends Model
{
    use HasCalendarAttributes;

    protected $table = 'working_jorney_schedules';

    protected $fillable = ['date', 'entry', 'exit'];

    protected $casts = [
        'date' => 'date',
        'entry' => 'string',
        'exit' => 'string',
    ];

    protected $appends = ['day_of_week'];

    /**
     * The working jorney that owns this Schedule
     */
    public function workingJorney() : BelongsTo
    {
        return $this->belongsTo(WorkingJorney::class);
    }
}
