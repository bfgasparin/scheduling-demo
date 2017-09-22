<?php

namespace App\Exceptions\Salon\Client\Booking;

use Bugsnag;
use App\Salon\Client\Booking;
use App\Exceptions\InvalidArgument;

/**
 * Thrown when someone tries to cancel a booking that is already canceled
 */
class AlreadyCanceled extends InvalidArgument implements Exception
{
    /** @var App\Salon\Client\Booking */
    protected $booking;

    /**
     * Create a new AlreadyCanceled exception.
     *
     * @param  Booking  $booking
     * @return void
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;

        parent::__construct(
            __(
                "The booking ':booking' is already canceled.",
                [
                    'booking' => $booking->id,
                ]
            )
        );
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
                'booking' => $this->booking->makeHidden('salon')->toArray(),
                'salon' => $this->booking->salon->toArray(),
            ]);
        });
    }
}
