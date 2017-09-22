<?php

namespace App\Exceptions;

use LogicException;

/**
 * Base Logic Argument Exception for the application
 */
class Logic extends LogicException implements ExceptionInterface
{
    use RendersResponse;
}
