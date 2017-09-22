<?php

namespace App\Auth;

use InvalidArgumentException;
use App\Auth\Authenticatable;
use Tymon\JWTAuth\JWTGuard as TymonJWtGuard;
use Illuminate\Contracts\Auth\Authenticatable as IlluminateAuthenticatable;

/**
 * JWTGuard guards the application with a
 * JWT token
 *
 * A user is identified in the JWT token, using the
 * following Chaims:
 *     sub:   The user identifier
 *     typ:   The type of the user (The model qualified classname)
 *
 * To see all accepted chaim fields of the JWT Token, see `config/jwt.php`
 *
 * @see TymonJWtGuard
 */
class JWTGuard extends TymonJWtGuard
{
    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if ($this->user !== null) {
            return $this->user;
        }

        if ($this->jwt->getToken() && $this->jwt->check()) {
            $id = $this->jwt->payload()->get('sub');

            $user = $this->provider->retrieveById($id);

            $this->validateUser($user);

            // if the user is the same type as declared inside the token.
            // he is certainly the authenticated user of the token,
            // so returned it
            if (get_class($user) == $this->jwt->getClaim('typ')) {
                return $this->user = $user;
            }

            return null;
        }
    }

    /**
     * Set the current user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return $this
     */
    public function setUser(IlluminateAuthenticatable $user)
    {
        $this->validateUser($user);

        return parent::setUser($user);
    }

    /**
     * Validates if the user is an Authenticatable instance
     *
     * @return void
     */
    protected function validateUser($user) : void
    {
        if (!empty($user) && !is_a($user, Authenticatable::class)) {
            throw new InvalidArgumentException(
                'The authenticated user instance must implements '.Authenticatable::class.' interface'
            );
        }
    }
}
