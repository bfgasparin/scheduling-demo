<?php

namespace Tests\Feature\API\ForUser;

use Bus;
use Carbon\Carbon;
use App\{User, Salon};
use Tests\Feature\API\CRUDTestCase;
use Tests\Concerns\SalonWorkerHelpers;
use App\Salon\{Client\Booking, Client};
use Tests\Feature\API\Concerns\UserCRUDTests;
use Tests\Concerns\SalonClientBookingHelpers;
use App\Jobs\Salon\Client\Booking\CreateThroughUser as CreateBookingThroughUser;
use Tests\Feature\API\Concerns\Validation\{
    RequiredRuleTests, DateRuleTests, DateFormatRuleTests, ExistsRuleTests, AfterOrEqualRuleTests
};

/**
 * Testing a user creating client Bookings
 *
 * @see App\User
 * @see App\Salon\Client\Booking
 * @see App\Salon\Service
 */
class CreateClientBookingsTest extends CRUDTestCase
{
    use SalonWorkerHelpers,
        SalonClientBookingHelpers,
        UserCRUDTests,
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
     * The client the authenticated user represents
     * @var App\Salon\Client
     */
    protected $client;

    /** @before */
    public function setUpClient() : void
    {
        $this->client = factory(Client::class)->create();
    }

    /**
     * Returns a Salon to be used on Tests
     * @see Tests\Concerns\SalonClientBookingHelpers
     *
     * @return App\Salon
     */
    protected function salon() : Salon
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
     * Returns the user used to authenticated to the App
     * @return App\User
     */
    public function authUser() : User
    {
        return $this->createUserWithClients($this->client);
    }

    /**
     * @test
     * @see Tests\Feature\API\Concerns\SimpleCRUDResourceTests
     */
    public function it_creates_a_new_resource()
    {
        $this->a_user_can_create_a_booking();
    }

    /** @test */
    public function a_user_can_create_a_booking() : void
    {
        Bus::fake();

        $this->actingAs($this->authUser, $this->authGuard())
            ->json('POST', "/api/salons/{$this->salon->id}/bookings", $input = $this->getInputData())
            ->assertStatus(202);

        Bus::assertDispatched(CreateBookingThroughUser::class, function ($job) use ($input) {
            return $job->user->is($this->authUser) &&
                $job->salon->is($this->salon) &&
                $job->data === $input;
        });
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
        return "salons/{$this->salon->id}/bookings";
    }

    /**
     * Returns the input data to use for tests with the resource
     * @see Tests\Feature\API\Concerns\SimpleCRUDResourceTests
     *
     * @return array
     */
    protected function inputData() : array
    {
        // get a booking data
        // with service and professinal of the salon Tested
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
        Bus::assertNotDispatched(CreateBookingThroughUser::class);
    }
}
