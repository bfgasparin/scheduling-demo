<?php

namespace App\Listeners\Salon\Client\Booking;

use App\Salon\Service;
use App\Exceptions\Logic;
use Illuminate\Support\Arr;
use App\Events\Salon\Client\Booking\Updating;
use App\Exceptions\Salon\Client\Booking\UpdateNotAllowed as BookingUpdateNotAllowed;

/**
 * A Booking can not be changed, only canceled.
 *
 * DenyChanges denies any change in a booking except the changes needed
 * to cancel the booking.
 */
class DenyChanges
{
    /**
     * Deny changes in the booking
     *
     * @param  Updating  $event
     * @return void
     */
    public function handle(Updating $event) : void
    {
        if (! empty(Arr::except($event->booking->getDirty(), 'canceled_at'))) {
            throw new BookingUpdateNotAllowed($event->booking);
        }
    }
}
