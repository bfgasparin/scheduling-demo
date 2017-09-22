<?php

namespace App\Policies\Salon;

use App\Salon\Employee;
use App\Auth\Authenticatable;
use App\Salon\Worker as SalonWorker;
use App\Policies\Concerns\HasSalonWorkerPolicies;

/**
 * Policies for Employee model
 */
class EmployeePolicy
{
    use HasSalonWorkerPolicies;

    /**
     * Determine whether the salon user can create an employee in the salon.
     *
     * @param  \App\Auth\Authenticatable  $user
     * @return mixed
     */
    public function create(Authenticatable $user)
    {
        return $this->check($user, true);
    }

    /**
     * Determine whether the authenticatable can update the employee.
     *
     * @param  \App\Auth\Authenticatable  $user
     * @param  \App\Salon\Employee  $employee
     * @return mixed
     */
    public function update(Authenticatable $user, Employee $employee)
    {
        return $this->check($user, function ($user) use ($employee) {
            // A user can update other non admin users only if its an admin
            if ($user->isAdmin() && !$employee->isAdmin()) {
                return true;
            }

            // otherwhise, it can update only itself
            return $employee->is($user);
        });

    }

    /**
     * Determine whether the salon user can delete an employee in the salon
     *
     * @param  \App\Auth\Authenticatable  $user
     * @return mixed
     */
    public function delete(Authenticatable $user)
    {
        return $this->check($user, true);
    }

    /**
     * Determine whether the salon user can view an employee in the salon
     *
     * @param  \App\Auth\Authenticatable  $user
     * @param  \App\Salon\Employee  $employee
     * @return mixed
     */
    public function view(Authenticatable $user, Employee $employee)
    {
        return $this->check($user, function ($user) use ($employee) {
            // A user can view an employee only if its an admin
            if ($user->isAdmin()) {
                return true;
            }

            // or if its himself
            return $employee->is($user);
        });
    }
}
