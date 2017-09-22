<?php

namespace App\Eloquent\Concerns\Auth;

use Auth;
use App\Salon;
use App\BelongsToSalon;
use Illuminate\Database\Eloquent\Builder;

/**
 * If the authenticated user belongs to a Salon, it scopes all queries to include only
 * models that belongs to the Salon of the authenticated user
 *
 * @see App\BelongsToSalon
 */
trait ScopedByAuthUserSalon
{
    public static function bootScopedByAuthUserSalon() : void
    {
        static::addGlobalScope('byAuthUserSalon', function (Builder $query) {
            $query->when(is_a(Auth::user(), BelongsToSalon::class), function (Builder $query) {
                return $query->where('salon_id', Auth::user()->salon_id);
            });
        });
    }
}
