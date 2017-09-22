<?php

namespace App\Eloquent\Concerns\Auth;

use Auth;
use App\BelongsToSalon;
use Illuminate\Database\Eloquent\Builder;

/**
 * If the authenticated user belongs to a Salon, it scopes all queries to include
 * only models that belongs to clients from the salon of the authenticated user
 * @see App\Eloquent\Concerns\Auth\ScopedByAuthUserSalon::byAuthUserSalon
 *
 * Should be used on Models owned by a Client
 * @see App\Salon\Client
 */
trait ScopedByAuthUserSalonThroughClient
{
    public static function bootScopedByAuthUserSalonThroughClient() : void
    {
        static::addGlobalScope('bySalonOfAuthUserThroughClient', function (Builder $query) {
            // When is a guest or a salon user, join client to filter by the
            // salon of the client that owns the model through the global scope
            // byAuthUserSalon of the client model
            $query->when(is_a(Auth::user(), BelongsToSalon::class), function (Builder $query) {
                return $query->has('client');
            });
        });
    }
}
