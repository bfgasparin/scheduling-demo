<?php

namespace App\Exceptions\Activation;

use App\Exceptions\InvalidArgument;

/**
 * Thrown when a token is invalid when activating an activatable model
 * @see App\Eloquent\Concerns\Activatable
 */
class InvalidActivationToken extends InvalidArgument
{
    /**
     * Create a new InvalidActivationToken exception.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(__('Invalid Activation Token'));
    }
}
