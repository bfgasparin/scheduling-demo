<?php

namespace App\Events\Salon\Client\Booking;

use App\Salon\Client\Booking;

/**
 * Dipached wheh updating a booking
 */
class Updating
{
    /**
     * @var Booking
     */
    public $booking;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }
}
