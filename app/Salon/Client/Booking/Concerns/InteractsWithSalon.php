<?php

namespace App\Salon\Client\Booking\Concerns;

use App\Salon;
use Carbon\Carbon;

/**
 * Adds some methods to help the booking interacts with the salon of the client
 */
trait InteractsWithSalon
{
    /**
     * The Salon this booking belongs
     *
     * @return App\Salon
     */
    public function getSalonAttribute() : Salon
    {
        return $this->client->salon;
    }

    /**
     * Returns if the booking exceeds the Cancel Tolerance time for Client User of the Salon
     * @see App\Salon\Config\Booking::cancel_tolerance_for_client_user
     *
     * @return bool
     */
    public function exceedsCancelToleranceForClientUser() : bool
    {
        return $this->salon->configBooking->isCancelToleranceForClientUserExceededWith(
            Carbon::today(),
            $this->start
        );
    }
}
