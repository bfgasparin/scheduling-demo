<?php

namespace App\Exceptions;

use InvalidArgumentException;

/**
 * Base Invalid Argument Exception for the application
 */
class InvalidArgument extends InvalidArgumentException implements ExceptionInterface
{
    use RendersResponse;
}
