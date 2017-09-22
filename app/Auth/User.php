<?php

namespace App\Auth;

use Illuminate\Foundation\Auth\User as IlluminateUser;

/**
 * Base class for models enabled to authenticate to the system.
 *
 * @see IIlluminate\Foundation\Auth\User
 * @see Authenticatable
 */
abstract class User extends IlluminateUser implements Authenticatable
{
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     * @see JWTSubject
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->id;
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     * @see JWTSubject
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return ['typ' => static::class];
    }
}
