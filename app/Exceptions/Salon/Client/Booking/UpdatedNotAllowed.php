<?php

namespace App\Exceptions\Salon\Client\Booking;

use Bugsnag;
use App\Exceptions\Exception;
use App\Salon\Client\Booking;

/**
 * Thrown when a booking is updated
 */
class UpdateNotAllowed extends Exception
{
    /** @var App\Salon\Client\Booking */
    protected $booking;

    /**
     * Create a new UpdateNotAllowed exception.
     *
     * @param App\Salon\Client\Booking $booking

     * @return void
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;

        parent::__construct(__(
            'The booking :booking is not allowed to be updated',
            ['booking' => $booking->id]
        ));
    }

    /**
     * Report the exception
     *
     * @return void
     */
    public function report() : void
    {
        Bugsnag::notifyException($this, function ($report) {
            $report->setSeverity('error');
            $report->setMetaData([
                'booking' => $this->booking->toArray(),
            ]);
        });
    }
}
