<?php

namespace App\Eloquent\Concerns\Auth;

use Auth;
use App\Salon\Client\User as ClientUser;
use Illuminate\Database\Eloquent\Builder;

/**
 * If the authenticated user is a Salon Client User, it scopes all queries to include only
 * models that belongs to clients represented by the user
 *
 * @see App\Salon\Client\User
 */
trait ScopedByAuthUserClients
{
    public static function bootScopedByAuthUserClients() : void
    {
        static::addGlobalScope('byAuthClientUser', function (Builder $query) {
            $query->when(is_a(Auth::user(), ClientUser::class), function (Builder $query) {
                return $query->whereIn('client_id', Auth::user()->getClients()->pluck('id'));
            });
        });
    }
}
