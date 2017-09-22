<?php

namespace App\Exceptions\Auth\User;

use Bugsnag;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * Throws when an inactive user tries to login
 * @see App\User
 */
class IsNotActive extends AuthorizationException
{
    /** @var App\User */
    public $user;

    /**
     * Create a new IsNotActive exception.
     *
     * @param  User  $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Report the exception
     *
     * @return void
     */
    public function report() : void
    {
        Bugsnag::notifyException($this, function ($report) {
            $report->setUser($this->user->toArray());
        });
    }
}
