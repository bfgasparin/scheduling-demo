<?php

namespace App\Eloquent\Concerns\Salon\Client\User;

use App\Salon;
use App\Salon\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Helps client users to represent a Salon Client
 * @see App\Salon\Client\User
 * @see App\Salon\Client
 */
trait RepresentsAClient
{
    use ManageClientBookings;

    /**
     * The salon clients the user represents
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clients() : HasMany
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Returns the Clients the user represents
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getClients() : Collection
    {
        return $this->clients;
    }

    /**
     * Returns the Client the user represents for the given Salon.
     *
     * Returns null if the user does no represent any client for the Salon
     *
     * @return App\Salon\Client|null
     */
    public function getClientFor(Salon $salon) : ?Client
    {
        return $this->clients()->where('salon_id', $salon->id)->first();
    }

    /**
     * Returns if the user represents the given Client
     *
     * @param Client $client
     *
     * @return bool
     */
    public function represents(Client $client) : bool
    {
        return $client->user->is($this);
    }

    /**
     * Becomes a Client of the given Salon
     *
     * @param App\Salon $salon
     *
     * @return App\Salon\Client
     */
    public function becomesClientOf(Salon $salon) : Client
    {
        return tap($salon->clients()->create($this->attributes), function ($client) {
            $client->user()->associate($this);
            $client->save();
        });
    }
}
