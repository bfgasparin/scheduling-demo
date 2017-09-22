<?php

namespace App\Policies\Concerns;

use App\Auth\Authenticatable;
use App\Salon\Worker as SalonWorker;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Helper for Policies that must be protected to Salon Workers
 */
trait HasSalonWorkerPolicies
{
    use HandlesAuthorization;

    /**
     * Check if the authenticated user is a Salon Worker and
     * then calls the given callback
     *
     * @param  \App\Auth\Authenticatable  $user
     * @param  mixed  $policies
     * @return mixed
     */
    protected function check(Authenticatable $user, $policies)
    {
        // only user that works on a salon can create a model
        if (!is_a($user, SalonWorker::class)) {
            return false;
        }

        // if the policies is a callable function, we assume
        // the function will check the permissions
        if (is_callable($policies)) {
            return with(is_null($policies) ? true : $policies($user));
        }

        // if the polcies is a a boolean value function, just return it
        if (is_bool($policies)) {
            return $policies;
        }

        // otherwise, deny the operation
        return false;
    }
}
