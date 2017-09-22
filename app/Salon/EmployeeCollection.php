<?php

namespace App\Salon;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * Collection for Employee models
 */
class EmployeeCollection extends EloquentCollection
{
    /**
     * Filters all professional employees
     *
     * @return self;
     */
    public function onlyProfessional() : self
    {
        return $this->filter->isProfessional();
    }

    /**
     * Filters all admin employees
     *
     * @param mixed $date
     *
     * @return self;
     */
    public function onlyAdmin() : self
    {
        return $this->filter->isAdmin();
    }
}
