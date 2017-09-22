<?php

namespace App\Eloquent\Concerns;

use Auth;
use App\Salon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Adds some helpful scopes to help filter Salon Workers
 */
trait SalonWorkerScopes
{
    /**
     * Scope a query to only include Admin Employees.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmin(Builder $query) : Builder
    {
        return $query->where('is_admin', true);
    }

    /**
     * Scope a query to only include professional employees.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProfessional(Builder $query) : Builder
    {
        return $query->where('is_professional', true);
    }
}
