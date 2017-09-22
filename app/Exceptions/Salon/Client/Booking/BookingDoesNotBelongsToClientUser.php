<?php

namespace App\Exceptions\Salon\Client\Booking;

use Bugsnag;
use App\Exceptions\Logic;
use App\Salon\Client\{User as ClientUser, Booking};

/**
 * Thrown when a client user does not own a given booking
 */
class BookingDoesNotBelongsToClientUser extends Logic implements Exception
{
    /** @var App\Salon\Client\User */
    public $user;

    /** @var App\Salon\Client\Booking  */
    public $booking;

    /**
     * Create a new BookingDoesNotBelongsToClientUser exception.
     *
     * @param ClientUser $user
     * @param Booking $booking

     * @return void
     */
    public function __construct(ClientUser $user, Booking $booking)
    {
        $this->user = $user;
        $this->booking = $booking;

        parent::__construct(
            __(
                "The booking ':booking' does not belongs to the client user ':user.'",
                [
                    'booking' => $booking->id,
                    'user' => "{$user->name} ({$user->id})",
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
                'client_user' => $this->user->load('clients')->makeHidden('bookings')->toArray(),
                'booking' => $this->user->toArray(),
            ]);
        });
    }
}
