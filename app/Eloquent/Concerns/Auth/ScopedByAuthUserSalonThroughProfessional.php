<?php

namespace App\Eloquent\Concerns\Auth;

use Auth;
use App\BelongsToSalon;
use Illuminate\Database\Eloquent\Builder;

/**
 * If the authenticated user belongs to a Salon, it scopes all queries to include
 * only models that belongs to professionals from the Salon of the Authenticated user
 * @see App\Eloquent\Concerns\Auth\ScopedByAuthUserSalon::byAuthUserSalon
 *
 * Should be used on Models owned by a Professional
 */
trait ScopedByAuthUserSalonThroughProfessional
{
    public static function bootScopedByAuthUserSalonThroughProfessional() : void
    {
        static::addGlobalScope('byAuthUserSalonThroughProfessional', function (Builder $query) {
            // When is a guest or a salon user, join professional to filter by the
            // salon of the professional that owns the model through the global scope
            // byAuthUserSalon of the professional model
            $query->when(is_a(Auth::user(), BelongsToSalon::class), function (Builder $query) {
                return $query->has('professional');
            });
        });
    }
}
