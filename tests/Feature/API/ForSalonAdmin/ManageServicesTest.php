<?php

namespace Tests\Feature\API\ForSalonAdmin;

use App\Salon;
use App\Salon\{Service, Employee};
use Tests\Feature\API\CRUDTestCase;
use App\Salon\Worker as SalonWorker;
use Tests\Concerns\SalonWorkerHelpers;
use Tests\Feature\API\Concerns\SalonWorkerCRUDTests;
use Tests\Feature\API\Concerns\Validation\{
    RequiredRuleTests, MaxRuleTests, IntegerRuleTests, NumericRuleTests,
    MinRuleTests, StringRuleTests, BetweenRuleTests, InRuleTests
};

/**
 * Testing Admin managing Salon's Services
 *
 * @see App\Salon\Service
 * @see CRUDTestCase
 */
class ManageServicesTest extends CRUDTestCase
{
    use SalonWorkerHelpers,
        SalonWorkerCRUDTests,
        RequiredRuleTests,
        MaxRuleTests,
        IntegerRuleTests,
        NumericRuleTests,
        MinRuleTests,
        StringRuleTests,
        BetweenRuleTests,
        InRuleTests;

    /**
     * Class name of the resource model to be used
     *
     * by the Simple CRUD tests
     * @see SimpleCRUDResourceTests
     */
    protected $model = Service::class;

    /**
     * The resource name on uri to be used by the Simple CRUD tests
     *
     * @see SimpleCRUDResourceTests
     */
    protected $resource = 'salonServices';

    /**
     * The table associated with the resource used by the Simple CRUD tests
     *
     * @see SimpleCRUDResourceTests
     */
    protected $table = 'salon_services';

    /**
     * CRUD operations to test for the resource
     *
     * @see SimpleCRUDResourceTests
     */
    protected $operations = ['create', 'update', 'delete', 'read'];

    /**
     * The fields that should be tested against the required validation rule
     *
     * @see RequiredRuleTests
     */
    protected $requiredRules = [
        'name', 'description', 'price', 'duration', 'cost'
    ];

    /**
     * The fields that should be tested against the max validation rule
     *
     * @see MaxRuleTests
     */
    protected $maxRules = [
            'string' => [['name', 100], ['description', 255]],
    ];

    /**
     * The fields that should be tested against the numeric validation rule
     *
     * @see NumericRuleTests
     */
    protected $numericRules = [
        'price', 'cost'
    ];

    /**
     * The fields that should be tested against the integer validation rule
     *
     * @see IntegerRuleTests
     */
    protected $integerRules = [
        'duration',
    ];

    /**
     * The fields that should be tested against the min validation rule
     *
     * @see MinRuleTests
     */
    protected $minRules = [
        'integer' => [['duration', 5]],
    ];

    /**
     * The fields that should be tested against the string validation rule
     *
     * @see StringRuleTests
     */
    protected $stringRules = [
        'name', 'description', 'client_visibility'
    ];

    /**
     * The fields that should be tested against the between validation rule
     *
     * @see BetweenRuleTests
     */
    protected $betweenRules = [
        'numeric' => [['price', 0, 999.99], ['cost', 0, 999.99]],
    ];

    /**
     * The fields that should be tested against the in validation rule
     *
     * @see inRuleTests
     */
    protected $inRules = [
        'client_visibility',
    ];

    /**
     * Returns the authenticated user to test the Resource CRUDs
     *
     * @return App\Salon\Worker
     */
    protected function salonAuthUser() : SalonWorker
    {
        return $this->createEmployeeAdmin(['salon_id' => $this->salon->id]);
    }

    /** @test */
    public function a_salon_admin_can_see_the_list_of_services()
    {
        // fixtures
        $services = factory(Service::class, 20)->create(['salon_id' => $this->authUser->salon_id]);

        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('GET', "/api/{$this->getCRUDResource()}")
            ->assertSuccessful()
            ->assertJsonPagination(
                $services->forPage(1,15)->toArray(),
                20
            );
    }

    /** @test */
    public function it_can_not_list_resources_from_another_salon()
    {
        factory($this->model, 5)->create();
        $models = factory($this->model, 20)->create(['salon_id' => $this->authUser->salon_id]);

        $this->actingAs($this->authUser, $this->authGuard())
            ->json('GET', "/api/{$this->getCRUDResource()}")
            ->assertSuccessful()
            ->assertJsonPagination(
                $models->forPage(1,15)->toArray(),
                20
            );
    }
}
