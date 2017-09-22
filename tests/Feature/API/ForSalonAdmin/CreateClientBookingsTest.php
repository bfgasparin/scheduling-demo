<?php

namespace Tests\Feature\API\ForSalonAdmin;

use Bus;
use App\Salon;
use Carbon\Carbon;
use Tests\Feature\API\CRUDTestCase;
use App\Salon\Worker as SalonWorker;
use App\Salon\{Client\Booking, Client};
use Tests\Feature\API\Concerns\SalonWorkerCRUDTests;
use Tests\Concerns\{SalonWorkerHelpers, SalonClientBookingHelpers};
use App\Jobs\Salon\Client\Booking\Create as CreateBooking;
use Tests\Feature\API\Concerns\Validation\{
    RequiredRuleTests, DateRuleTests, DateFormatRuleTests, ExistsRuleTests, AfterOrEqualRuleTests
};

/**
 * Testing an Admin creating bookings for its clients
 *
 * @see App\Salon\Client
 * @see App\Salon\Client\Booking
 * @see App\Salon\Service
 */
class CreateClientBookingsTest extends CRUDTestCase
{
    use SalonWorkerHelpers,
        SalonClientBookingHelpers,
        SalonWorkerCRUDTests,
        RequiredRuleTests,
        DateRuleTests,
        DateFormatRuleTests,
        ExistsRuleTests,
        AfterOrEqualRuleTests;

    /**
     * Class name of the resource model to be used by the Simple CRUD tests
     * @see Tests\Feature\API\Concerns\CRUDResourceTestable
     */
    protected $model = Booking::class;

    /**
     * CRUD operations to test for the resource
     *
     * @see Tests\Feature\API\Concerns\Validation\AssertsRules
     */
    protected $operations = ['create'];

    /**
     * The fields that should be tested against the required validation rule
     *
     * @see RequiredRuleTests
     */
    protected $requiredRules = [
        'date', 'service_id', 'professional_id', 'start'
    ];

    /*
     * The fields that should be tested against the date validation rule
     *
     * @see DateRuleTests
     */
    protected $dateRules = [
        'date'
    ];

    /*
     * The fields that should be tested against the exists validation rule
     *
     * @see existsRuleTests
     */
    protected $existsRules = [
        'service_id', 'professional_id'
    ];

    /**
     * The fields that should be tested against the date_format validation rule
     *
     * @see DateFormatRuleTests
     */
    protected $dateFormatRules = [
        ['start', 'H:i:s'],
    ];

    /**
     * The fields that should be tested against the after_or_equal validation rule
     *
     * @see AfterOrEqualRuleTests
     */
    protected $afterOrEqualRules = [
        ['start', 'today'],
    ];

    /**
     * The client to booking services
     * @var Salon
     */
    protected $client;

    /** @before */
    protected function setUpClient() : void
    {
        $this->client = factory(Client::class)->create();
    }

    /**
     * Returns a Salon to be used on test the Resource CRUDs
     * @see Tests\Feature\API\Concerns\SalonWorkerCRUDTests
     *
     * @return App\Salon
     */
    public function salon() : Salon
    {
        return $this->client->salon;
    }

    /**
     * Set Up Application before create resource test
     * @see Tests\Feature\API\Concerns\Validation\AssertsValidation
     * @see Tests\Feature\API\Concerns\SimpleCRUDResourceTests
     *
     * @return void
     */
    protected function setUpAppBeforeCreateResource() : void
    {
        Bus::fake();
        $this->actingAs($this->authUser, $this->authGuard());
    }

    /**
     * @test
     * @see Tests\Feature\API\Concerns\SimpleCRUDResourceTests
     */
    public function it_creates_a_new_resource()
    {
        $this->a_salon_admin_can_create_a_booking();
    }

    /** @test */
    public function a_salon_admin_can_create_a_booking()
    {
        Bus::fake();

        $this->actingAs($this->authUser, $this->authGuard())
            ->json('POST', "api/clients/{$this->client->id}/bookings", $input = $this->getInputData())
            ->assertStatus(202);

        Bus::assertDispatched(CreateBooking::class, function ($job) use ($input) {
            return $job->client->is($this->client) &&
                $job->data === array_merge($input, ['salon_id' => $this->authUser->salon_id]);
        });
    }

    /** @test */
    public function a_salon_admin_can_not_create_a_booking_on_another_salon()
    {
        Bus::fake();
        $salon = factory(Salon::class)->create(); $this->actingAs($this->authUser, $this->authGuard())
            ->json('POST', "/api/salons/{$salon->salon_id}/bookings", $this->inputData())
            ->assertStatus(404);

        Bus::assertNotDispatched(CreateBookingThroughUser::class);
    }

    /**
     * Returns the authenticated user to test the Resource CRUDs
     *
     * @return App\Salon\Worker
     */
    protected function salonAuthUser() : SalonWorker
    {
        return $this->createEmployeeAdmin(['salon_id' => $this->salon->id]);
    }

    /**
     * Returns the name of the resource to test the CRUDs
     *
     * @see Tests\Feature\API\Concerns\CRUDResourceTestable
     *
     * @return string
     */
    protected function getCRUDResource() : string
    {
        return "clients/{$this->client->id}/bookings";
    }

    /**
     * Returns the input data to use for tests with the resource
     * @see Tests\Feature\API\CRUDTestCase
     *
     * @return array
     */
    protected function inputData() : array
    {
        // get a booking data with a service and a professional for the tested salon
        return $this->bookingData(
            ['date' => Carbon::today()->addDays(rand(1,30))
        ]);
    }

    /**
     * Assert the application after the validation test on create CRUD operation
     * @see Tests\Feature\API\Concerns\Validation\AssertsValidation
     *
     * @return void
     */
    protected function assertAppAfterCreateFailure() : void
    {
        Bus::assertNotDispatched(CreateBooking::class);
    }
}
