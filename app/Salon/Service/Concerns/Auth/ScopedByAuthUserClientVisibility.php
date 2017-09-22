<?php

namespace App\Salon\Service\Concerns\Auth;

use Auth;
use App\Salon;
use App\BelongsToSalon;
use App\Salon\Client\User as ClientUser;
use Illuminate\Database\Eloquent\Builder;

/**
 * If the authenticated user is a Client User, it scopes all queries to include only models
 * acording to the service client visibility rule
 * @see App\Salon\Service
 *
 * Note: Guests (non authenticated users) are considered Salon Client Users because they also
 * can book services the same manner as Authenticated Client Users can.
 *
 * So Client Users are:
 * - Any class that implements App\Salon\Client\User interface
 * - A Guest user in the system
 *
 * Rules for Client Visibility:
 *
 * | Value  | Rule                   |
 * | ------ | ---------------------  |
 * | always | client can always view |
 * | never  | client can always view |
 *
 * @see App\Salon\Client
 * @see Auth::guest()
 *
 */
trait ScopedByAuthUserClientVisibility
{
    public static function bootScopedByAuthUserClientVisibility() : void
    {
        static::addGlobalScope('byAuthUserClientVisibility', function (Builder $query) {
            $query->when(Auth::guest() || is_a(Auth::user(), ClientUser::class), function (Builder $query) {
                return $query->alwaysVisibleForClients();
            });
        });
    }

    /**
     * Scope a query to only include service a client can always view
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAlwaysVisibleForClients(Builder $query)
    {
        return $query->where('client_visibility', 'always');
    }
}
