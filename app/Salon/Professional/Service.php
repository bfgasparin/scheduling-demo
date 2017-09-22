<?php

namespace App\Salon\Professional;

use App\Salon\{Employee, Service as SalonService};
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The relation between Professional and Service.
 *
 * Defines which service each professional can offer.
 *
 * @see App\Salon\Service
 */
class Service extends Pivot
{
    protected $hidden = ['service'];

    /**
     * Get the duration of the service offered by the professional.
     * If no duration was set, returns the default service duration
     *
     * @return int
     */
    public function getDurationAttribute($value) : int
    {
        return ! empty($value) ? $value : $this->service->duration;
    }

    /**
     * The service offered by the professional
     *
     * @return BelongsTo
     */
    public function service() : BelongsTo
    {
        return $this->belongsTo(SalonService::class);
    }

    /**
     * The professional that offers the service
     *
     * @return BelongsTo
     */
    public function professional() : BelongsTo
    {
        return $this->belongsTo(Employee::class)->professional();
    }
}
