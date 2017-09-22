<?php

namespace Tests\Feature\API\ForSalonAdmin;

use App\Salon;
use App\Salon\Employee;
use Illuminate\Support\Collection;
use Tests\Feature\API\CRUDTestCase;
use App\Salon\Worker as SalonWorker;
use Tests\Concerns\SalonWorkerHelpers;
use Tests\Feature\API\Concerns\SalonWorkerCRUDTests;
use Tests\Feature\API\Concerns\Validation\{RequiredRuleTests, MaxRuleTests, EmailRuleTests, CaseDiffRuleTests, NumbersRuleTests, StringRuleTests};

/**
 * Testing Admin managing the employees in in the salon
 *
 * @see App\Salon\Employee
 * @see CRUDTestCase
 */
class ManageEmployeesTest extends CRUDTestCase
{
    use SalonWorkerHelpers,
        SalonWorkerCRUDTests,
        RequiredRuleTests,
        MaxRuleTests,
        EmailRuleTests,
        CaseDiffRuleTests,
        NumbersRuleTests,
        StringRuleTests;

    /**
     * Class name of the resource model to be used by the Simple CRUD tests
     * @see SimpleCRUDResourceTests
     */
    protected $model = Employee::class;

    /**
     * The resource name on uri to be used by the Simple CRUD tests
     *
     * @see SimpleCRUDResourceTests
     */
    protected $resource = 'employees';

    /**
     * The table associated with the resource used by the Simple CRUD tests
     *
     * @see SimpleCRUDResourceTests
     */
    protected $table = 'salon_employees';

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
        'create' => ['name', 'email', 'password'],
        'update' => ['name', 'email']
    ];

    /**
     * The fields that should be tested against the email validation rule
     *
     * @see RequiredRuleTests
     */
    protected $emailRules = [
        'email'
    ];

    /**
     * The fields that should be tested against the email validation rule
     *
     * @see CaseDiffRuleTests
     */
    protected $caseDiffRules = [
        'create' => ['password'],
    ];

    /**
     * The fields that should be tested against the numbers validation rule
     *
     * @see NumbersRuleTests
     */
    protected $numbersRules = [
        'create' => ['password'],
    ];

    /**
     * The fields that should be tested against the max validation rule
     *
     * @see MaxRuleTests
     */
    protected $maxRules = [
        'create' => [
            'string' => [['name', 100], ['password', 60]],
        ],
        'update' => [
            'string' => [['name', 100]],
        ]
    ];

    /**
     * The fields that should be tested against the string validation rule
     *
     * @see StringRuleTests
     */
    protected $stringRules = [
        'name',
    ];

    /** @test */
    public function a_salon_admin_can_update_itself() : void
    {
        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('PUT', "/api/{$this->getCRUDResource()}/{$this->authUser->id}", $newData = $this->getInputData())
            ->assertSuccessful();

        $this->assertDatabaseHas(
            $this->getCRUDTable(),
            array_merge(['id' => $this->authUser->id], $this->getDatabaseDataAfterUpdate($newData, $this->authUser))
        );
    }

    /** @test */
    public function a_salon_admin_can_not_update_other_admin_employees() : void
    {
        // fixtures
        $otherEmployee = $this->createEmployeeAdmin(['salon_id' => $this->authUser->salon_id]);

        // updates its own data
        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('PUT', "/api/{$this->getCRUDResource()}/{$this->authUser->id}", with($newData = $this->getInputData()))
            ->assertSuccessful();

        $this->assertDatabaseHas(
                $this->getCRUDTable(),
                array_merge(['id' => $this->authUser->id], $this->getDatabaseDataAfterUpdate($newData, $this->authUser))
            );

        // try to update data from other admin employee
        $this->json('PUT', "/api/{$this->getCRUDResource()}/{$otherEmployee->id}", $this->getInputData())
            ->assertStatus(403);

        $this->assertDatabaseHas($this->getCRUDTable(), $otherEmployee->toArray());
    }

    /** @test */
    public function a_salon_admin_can_update_other_not_admin_employees() : void
    {
        // fixtures
        $otherEmployee = $this->createEmployeeNotAdmin(['salon_id' => $this->authUser->salon_id]);

        // updates its own data
        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('PUT', "/api/{$this->getCRUDResource()}/{$otherEmployee->id}", with($newData = $this->getInputData()))
            ->assertSuccessful();

        $this->assertDatabaseHas(
            $this->getCRUDTable(),
            array_merge(['id' => $otherEmployee->id], $this->getDatabaseDataAfterUpdate($newData, $otherEmployee))
        );
    }

    /** @test */
    public function a_salon_admin_can_delete_other_not_admin_employees() : void
    {
        // fixtures
        $otherEmployee = $this->createEmployeeNotAdmin(['salon_id' => $this->authUser->salon_id]);

        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('DELETE', "/api/{$this->getCRUDResource()}/{$otherEmployee->id}")
            ->assertSuccessful();

        $this->assertSoftDeleted($this->getCRUDTable(), $otherEmployee->toArray());
    }

    /**
     * @test
     * @see Tests\Feature\API\Concerns\SalonWorkerCRUDTests
     */
    public function it_can_not_list_resources_from_another_salon()
    {
        // Given we and a bunch of existing employees of another salons
        factory($this->model, 5)->create();

        // And Given we have a bunch of existing employees of the salon
        $employees = Collection::times(20, function () {
            return $this->existingResource();
        });

        $this->actingAs($this->authUser, $this->authGuard())
            ->json('GET', "/api/{$this->getCRUDResource()}")
            ->assertSuccessful()
            ->assertJsonPagination(
                $employees->push($this->authUser->makeHidden('salon'))->sortBy('id')->forPage(1, 15)->toArray(), // The Auth User also apperas in the list
                21
            );
    }

    /**
     * @test
     * @see Tests\Feature\API\Concerns\{SimpleCRUDResourceTests
     * */
    public function it_list_the_resource()
    {
        // Given we have a bunch of existing employees of the salon
        $employees = Collection::times(20, function () {
            return $this->existingResource();
        });

        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('GET', "/api/{$this->getCRUDResource()}")
            ->assertSuccessful()
            ->assertJsonPagination(
                $employees->push($this->authUser->makeHidden('salon'))->sortBy('id')->forPage(1, 15)->toArray(), // The Auth User also apperas in the list
                21
            );
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
     * Returns the existing Resource to change on
     * operation CRUD resource
     *
     * @return Employee
     */
    protected function existingResource() : Employee
    {
        // return user on the same salon as the authenticated user
        return $this->createProfessionalNotAdmin([
            'salon_id' => $this->authUser->salon_id
        ]);
    }

    /**
     * Returns the input data to use for tests with the resource
     *
     * @return array
     */
    protected function inputData() : array
    {
        return factory(Employee::class)->make()
            ->makeVisible('password')
            ->toArray();
    }

    /**
     * Returns the data to expect to be into database after the insert resource test success
     *
     * @return array
     */
    protected function databaseData(array $inputData) : array
    {
        // Can't check password. It s generated by a hash cypher and can not be reduplicated
        unset($inputData['password']);
        // We check here if the salon of the employee is the same as the logged user
        $inputData['salon_id'] = $this->authUser->salon_id;
        // the is_admin is not fillable
        $inputData['is_admin'] = false;
        // the is_professional is not fillable
        $inputData['is_professional'] = true;

        return $inputData;
    }

    /**
     * Returns the data to expect to contains on response after the resource create or update test success
     *
     * @param array $inputData  The request input data
     * @return array
     */
    protected function responseData(array $inputData) : array
    {
        return $this->databaseData($inputData);
    }

    /**
     * Returns data to expect into database after the update resource test success
     *
     * @param array $inputData The request input data
     * @param Employee $employee The existing Employee in the database
     * @return array
     */
    protected function databaseDataAfterUpdate(array $inputData, Employee $employee) : array
    {

        $inputData['salon_id'] = $employee->salon_id; // We check here if the salon of the employee is the same as before
        $inputData['password'] = $employee->password; // the password is not updatedable
        $inputData['is_admin'] = $employee->is_admin; // the is_admin is not updatedable
        $inputData['is_professional'] = $employee->is_professional; // the is_professional is not updatedable

        return $inputData;
    }

}
