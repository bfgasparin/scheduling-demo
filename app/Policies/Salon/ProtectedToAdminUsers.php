<?php

namespace App\Policies\Salon;

use App\Auth\Authenticatable;
use App\Policies\Concerns\HasSalonWorkerPolicies;

/**
 * Policy that determines that only Salon Admin users has permission to operate
 */
class ProtectedToAdminUsers
{
    use HasSalonWorkerPolicies;

    /**
     * Determine whether the user can create a resource
     *
     * @param  \App\Auth\Authenticatable  $user
     * @return mixed
     */
    public function create(Authenticatable $user)
    {
        return $this->check($user, function ($user) {
            // only Salon admin can create the model
            return $user->isAdmin();
        });
    }

    /**
     * Determine whether the user can update a resource
     *
     * @param  \App\Auth\Authenticatable  $user
     * @return mixed
     */
    public function update(Authenticatable $user)
    {
        return $this->check($user, function ($user) {
            // only Salon admin can update the model
            return $user->isAdmin();
        });
    }

    /**
     * Determine whether the user can delete a resource
     *
     * @param  \App\Auth\Authenticatable  $user
     * @return mixed
     */
    public function delete(Authenticatable $user)
    {
        return $this->check($user, function ($user) {
            // only Salon admin can delete the model
            return $user->isAdmin();
        });
    }
}
