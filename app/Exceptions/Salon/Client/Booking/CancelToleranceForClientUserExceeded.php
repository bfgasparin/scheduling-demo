<?php

namespace App\Exceptions\Salon\Client\Booking;

use Bugsnag;
use Carbon\Carbon;
use App\Salon\Client\Booking;
use App\Exceptions\InvalidArgument;

/**
 * Thrown when a client user tries to cancel a booking that exceeds the Cancel Tolerance time for
 * Client User of the Salon
 */
class CancelToleranceForClientUserExceeded extends InvalidArgument implements Exception
{

    /** @var App\Salon\Client\Booking */
    protected $booking;

    /**
     * Create a new CancelToleranceForClientUserExceeded exception.
     *
     * @param  Booking  $booking
     * @return void
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;

        parent::__construct(
            __(
                "The booking ':booking' can not be canceled with less than :cancelToleranceForClientUser minutes to start.",
                [
                    'booking' => $booking->id,
                    'cancelToleranceForClientUser' => $booking->salon->configBooking->cancel_tolerance_for_client_user,
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
            $report->setSeverity('info');
            $report->setMetaData([
                'cancel' => ['date' => Carbon::now()->toDateTimeString()],
                'booking' => $this->booking->makeHidden('salon')->toArray(),
                'salon' => $this->booking->salon->toArray(),
            ]);
        });
    }
}
