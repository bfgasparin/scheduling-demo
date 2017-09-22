<?php

namespace App\Auth;

use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Represents a user enabled to authenticate to the system.
 *
 * @see JWTSubject
 */
interface Authenticatable extends JWTSubject
{
}
