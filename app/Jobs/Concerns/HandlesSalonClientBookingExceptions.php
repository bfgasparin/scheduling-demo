<?php

namespace App\Jobs\Concerns;

use Exception;
use App\Exceptions\Salon\Client\Booking\Exception as BookingException;

/**
 * Help jobs to handle Salon Client Booking Exceptions
 */
trait HandlesSalonClientBookingExceptions
{
    /**
     * Returns if the exception is a client booking domain logic exception.
     * Domain Logic exceptions are  exceptions thrown when a booking businness
     * logic was broken.
     *
     * @param Exception $exception
     *
     * @return bool
     */
    protected function isBookingDomainLogicException(Exception $exception) : bool
    {
        return is_a($exception, BookingException::class);
    }
}


