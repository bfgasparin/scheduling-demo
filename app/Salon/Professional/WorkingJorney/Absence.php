<?php

namespace App\Salon\Professional\WorkingJorney;

use Illuminate\Database\Eloquent\Model;
use App\Salon\Professional\WorkingJorney;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Salon\Professional\WorkingJorney\Absence\Concerns\HasCalendarAttributes;

/**
 * Represents an absence from work.
 *
 * When a professional misses work, or want to register a future
 * absence from work, a new instance of Absence must be created
 *
 * @see App\Salon\Professional\WorkingJorney
 */
class Absence extends Model
{
    use HasCalendarAttributes;

    protected $table = 'working_jorney_absences';

    protected $fillable = ['date', 'observation'];

    protected $casts = [
        'date' => 'date'
    ];

    /**
     * The working jorney that owns this Absence
     */
    public function workingJorney() : BelongsTo
    {
        return $this->belongsTo(WorkingJorney::class);
    }
}
