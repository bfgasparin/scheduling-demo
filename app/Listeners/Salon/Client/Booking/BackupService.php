<?php

namespace App\Listeners\Salon\Client\Booking;

use App\Salon\Service;
use App\Events\Salon\Client\Booking\Creating;

/**
 * When a service is booked, it's important to backup some data related to the service.
 * This data should be used on future operations of the system.
 *
 * We can not allow, for example, that the system gives a service price to a client when
 * he was booking that service, and then charge the client another service price after he
 * consumed the booked service.
 * We need to charge the same service price as it was at service booking moment.
 *
 * This listener is responsable to gather and save this service information
 */
class BackupService
{
    /**
     * Backup some booking's service data.
     *
     * @param  Creating  $event
     * @return void
     */
    public function handle(Creating $event) : void
    {
        tap($event->booking, function ($booking) {
            $booking->service_price = Service::find($booking->service_id)->price;
        });
    }
}
