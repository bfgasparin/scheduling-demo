<?php

namespace Tests\Feature\API\ForSalonProfessional;

use App\Salon\Client;
use App\Salon\Worker as SalonWorker;
use Tests\Feature\API\ForSalonAdmin\SeeClientBookingsTest as BaseTestCase;

/**
 * Testing a Professional seeing bookings of clients of the salon
 *
 * @see App\Salon\Client
 * @see App\Salon\Client\Booking
 * @see App\Salon\Service
 */
class SeeClientBookingsTest extends BaseTestCase
{
    /**
     * The authGuard used to login the user booking the service
     *
     * @var string
     */
    protected $authGuard = 'api-salon-professionals';

    /** @before */
    public function setUpClient() : void
    {
        $this->client = factory(Client::class)->create();
    }

    /** @before */
    public function setUpAuthUser() : void
    {
        $this->authUser = $this->createProfessionalNotAdmin(['salon_id' => $this->client->salon_id]);
    }
}
