<?php

namespace App\Policies\Salon;

use App\Auth\Authenticatable;
use App\Policies\Concerns\HasSalonWorkerPolicies;
use App\Salon\{Client, Client\User as ClientUser};

/**
 * Policies for Client model
 */
class ClientPolicy
{
    use HasSalonWorkerPolicies;

    /**
     * Determine whether the user can view a client
     *
     * @param  \App\Auth\Authenticatable  $user
     * @param  \App\Salon\Client  $client
     * @return mixed
     */
    public function view(Authenticatable $user, Client $client)
    {
        if (is_a($user, ClientUser::class) && $user->represents($client)) {
            return true;
        }

        return $this->check($user, function ($user) {
            // Any salon worker can create booking
            return true;
        });
    }
}
