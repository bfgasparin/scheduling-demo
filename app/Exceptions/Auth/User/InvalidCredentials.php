<?php

namespace App\Exceptions\Auth\User;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Throws when the credentials in user authentication is invalid
 * @see App\User
 */
class InvalidCredentials extends UnauthorizedHttpException
{
    /**
     * Constructor.
     *
     * @param string     $challenge WWW-Authenticate challenge string
     * @param \Exception $previous  The previous exception
     * @param int        $code      The internal exception code
     */
    public function __construct($challenge, \Exception $previous = null, $code = 0)
    {
        parent::__construct($challenge, __('Invalid Credentials'), $previous, $code);
    }
}
