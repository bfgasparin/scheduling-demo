<?php

namespace App\Eloquent\Concerns;

use App\Salon;
use App\Eloquent\Concerns\SalonWorkerScopes;

/**
 * Contains methods to interact with Salon
 * workflow
 *
 * @see App\Salon
 * @see App\Eloquent\Concerns\HasSalon
 * @see App\Eloquent\Concerns\SalonWorkerScopes
 */
trait WorksOnSalon
{
    use HasSalon,
        SalonWorkerScopes;

    /**
     * Returns if the employee is Admin
     *
     * @return bool
     */
    public function isAdmin() : bool
    {
        return $this->is_admin;
    }

    /**
     * Returns ij the employee is a Professional
     *
     * @return bool
     */
    public function isProfessional() : bool
    {
        return $this->is_professional;
    }
}
