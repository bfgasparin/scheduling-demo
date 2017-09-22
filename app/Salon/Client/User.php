<?php

namespace App\Salon\Client;

use App\Salon;
use App\Salon\Client;
use App\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;

/**
 * A System User who can represent Salon Clients
 * @see App\Auth\Authenticatable
 * @see App\Salon\Client
 *
 * The implementation of this interface should be some user
 * who can log in to the system and represents a Client of a Salon.
 *
 * A User can represent clients for different salons,
 * but can represent only one unique client for each Salon.
 *
 * @see App\Salon
 */
interface User extends Authenticatable
{
    /**
     * Returns the Clients the user represents
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getClients() : Collection;

    /**
     * Returns the Client the user represents for the given Salon.
     *
     * Returns null if the user does no represent any client for the Salon
     *
     * @return App\Salon\Client|null
     */
    public function getClientFor(Salon $salon) : ?Client;

    /**
     * Returns if the user represents the given Client
     *
     * @param Client $client
     *
     * @return bool
     */
    public function represents(Client $client) : bool;

    /**
     * Becomes a Client of the given Salon
     *
     * @param App\Salon $salon
     *
     * @return App\Salon\Client
     */
    public function becomesClientOf(Salon $salon) : Client;
}
