<?php

namespace App\Exceptions;

use Exception as BaseException;

/**
 * Base Exception for the application
 */
class Exception extends BaseException implements ExceptionInterface
{
    use RendersResponse;
}
