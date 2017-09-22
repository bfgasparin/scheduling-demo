<?php

namespace Tests\Feature\API\ForSalonProfessional;

use App\Salon\Worker as SalonWorker;
use Tests\Feature\API\ForSalonAdmin\CreateClientBookingsTest as BaseTestCase;

/**
 * Testing a Professional creating bookings of clients of the salon
 *
 * @see App\Salon\Client
 * @see App\Salon\Client\Booking
 * @see App\Salon\Service
 */
class CreateClientBookingsTest extends BaseTestCase
{
    /**
     * The authGuard used to login the user booking the service
     *
     * @var string
     */
    protected $authGuard = 'api-salon-professionals';

    /**
     * Returns the authenticated user to test the Resource CRUDs
     *
     * @return App\Salon\Worker
     */
    protected function salonAuthUser() : SalonWorker
    {
        return $this->createProfessionalNotAdmin(['salon_id' => $this->client->salon_id]);
    }

}
