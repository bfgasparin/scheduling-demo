<?php

namespace App\Policies\Salon\Professional;

use App\Salon\Professional\WorkingJorney;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Policies\Concerns\HasSalonWorkerPolicies;

/**
 * Policies for WorkingJorney model
 */
class WorkingJorneyPolicy
{
    use HasSalonWorkerPolicies;

    /**
     * Determine whether the user can create a WorkingJorney for a professional
     *
     * @param  \App\Auth\Authenticatable  $user
     * @return mixed
     */
    public function create(Authenticatable $user)
    {
        return $this->check($user, true);
    }

    /**
     * Determine whether the user can update the WorkingJorney of a professional
     *
     * @param  \App\Auth\Authenticatable  $user
     * @return mixed
     */
    public function update(Authenticatable $user, WorkingJorney $workingJorney)
    {
        return $this->check($user, function ($user) use ($workingJorney) {
            if ($user->isAdmin()) {
                return true;
            }
            // only the professional that owns the WorkingJorney can update the WorkingJorney
            return $workingJorney->professional->is($user);
        });
    }

    /**
     * Determine whether the user can delete the WorkingJorney of a professional
     *
     * @param  \App\Auth\Authenticatable  $user
     * @return mixed
     */
    public function delete(Authenticatable $user, WorkingJorney $workingJorney)
    {
        return $this->check($user, function ($user) use ($workingJorney) {
            if ($user->isAdmin()) {
                return true;
            }
            // only the professional that owns the WorkingJorney can delete the WorkingJorney
            return $workingJorney->professional->is($user);
        });
    }

    /**
     * Determine whether the user can view the WorkingJorney of a professional
     *
     * @param  \App\Auth\Authenticatable  $user
     * @return mixed
     */
    public function view(Authenticatable $user, WorkingJorney $workingJorney)
    {
        return $this->check($user, function ($user) use ($workingJorney) {
            if ($user->isAdmin()) {
                return true;
            }
            // only the professional that owns the WorkingJorney can delete the WorkingJorney
            return $workingJorney->professional->is($user);
        });
    }
}
