<?php

namespace App\Policies;

use App\User;
use App\Auth\Authenticatable;
use App\Policies\Concerns\HasSalonWorkerPolicies;

/**
 * Policies for User model
 */
class UserPolicy
{
    use HasSalonWorkerPolicies;

    /**
     * Determine whether the auth user can view a User model
     *
     * @param  \App\Auth\Authenticatable  $user
     * @param  \App\Salon\User  $user
     * @return mixed
     */
    public function view(Authenticatable $authUser, User $user)
    {
        if ($authUser->is($user)) {
            return true;
        }

        return $this->check($user, function ($user) {
            if ($user->isAdmin()) {
                return true;
            }
        });
    }
}
