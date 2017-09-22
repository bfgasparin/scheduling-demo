<?php

namespace Tests\Feature\API\ForSalonProfessional;

use App\Salon;
use App\Salon\Employee;
use Tests\Feature\API\CRUDTestCase;
use App\Salon\Worker as SalonWorker;
use Tests\Concerns\SalonWorkerHelpers;
use Tests\Feature\API\Concerns\SalonWorkerCRUDTests;
use Tests\Feature\API\Concerns\Validation\{RequiredRuleTests, MaxRuleTests, EmailRuleTests, StringRuleTests};

/**
 * Testing Professional managing other employees in the salon
 *
 * @see CRUDTestCase
 * @see App\Salon\Employee
 * @see App\Http\Controllers\API\Salon\ProfileController
 */
class ManageEmployeesTest extends CRUDTestCase
{
    use SalonWorkerHelpers,
        SalonWorkerCRUDTests,
        RequiredRuleTests,
        MaxRuleTests,
        EmailRuleTests,
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
    protected $operations = ['update'];

    /**
     * The Auth Guard to authenticated the user
     *
     * @see Tests\Feature\API\Concerns\SalonWorkerCRUDTests::authGuard
     *
     * @var string
     */
    protected $authGuard = 'api-salon-professionals';

    /**
     * The fields that should be tested against the required validation rule
     *
     * @see RequiredRuleTests
     */
    protected $requiredRules = [
        'name', 'email'
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
     * The fields that should be tested against the max validation rule
     *
     * @see MaxRuleTests
     */
    protected $maxRules = [
       'string' => [['name', 100]],
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
    public function a_professional_can_not_see_the_list_of_employees()
    {
        // given there is a list of employees
        factory(Employee::class, 5)->create(['salon_id' => $this->authUser->salon_id]);

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('GET', "/api/employees")
            ->assertStatus(401);
    }

    /** @test */
    public function a_professional_can_not_see_any_employee()
    {
        // given there is a list of employees
        $employees = factory(Employee::class, 5)->create(['salon_id' => $this->authUser->salon_id]);

        $this->actingAs($this->authUser, 'api-salon-professionals');

        $employees->each(function ($employee) {
            $this->json('GET', "/api/employees/{$employee->id}")
                ->assertStatus(403);
        });
    }

    /** @test */
    public function a_professional_can_not_create_other_employees() : void
    {
        $this->actingAs($this->authUser, 'api-salon-professionals')
             ->json('POST', "/api/employees", $inputData = factory(Employee::class)->make()->toArray())
             ->assertStatus(401);

        $this->assertDatabaseMissing('salon_employees', collect($inputData)->except('salon_id')->toArray());
    }

    /** @test */
    public function a_professional_can_not_delete_any_employee()
    {
        // given there is a bunch of employees
        $employees = factory(Employee::class, 5)->create(['salon_id' => $this->authUser->salon_id]);

        $this->actingAs($this->authUser, 'api-salon-professionals');

        $employees->each(function ($employee) {
            $this->json('DELETE', "/api/employees/{$employee->id}")
                ->assertStatus(401);
        });
    }

    /** @test */
    public function a_professional_can_not_update_another_employee()
    {
        // given there is a bunch of employees
        $employees = factory(Employee::class, 5)->create(['salon_id' => $this->authUser->salon_id]);

        $this->actingAs($this->authUser, 'api-salon-professionals');

        $employees->each(function ($employee) {
            $this->json('PUT', "/api/employees/{$employee->id}",
                with($input = collect(factory(Employee::class)->make(['salon_id' => $this->authUser->salon_id])))->toArray()
            )->assertStatus(403);

            $this->assertDatabaseHas('salon_employees', $employee->toArray())
                ->assertDatabaseMissing('salon_employees', $input->merge(['id' => $employee->id])->toArray());
        });
    }

    /** @test */
    public function a_professional_can_see_its_own_data() : void
    {
        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('GET', "/api/employees/{$this->authUser->id}")
             ->assertSuccessful()
             ->assertJsonFragment($this->authUser->makeHidden('salon')->toArray());
    }

    /** @test */
    public function a_professional_can_update_its_own_data() : void
    {
        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('PUT', "/api/employees/{$this->authUser->id}", $newData = $this->inputData())
            ->assertSuccessful();

        $this->assertDatabaseHas(
            $this->getCRUDTable(),
            array_merge(['id' => $this->authUser->id], $this->getDatabaseDataAfterUpdate($newData, $this->authUser))
        );
    }

    /**
     * Returns the authenticated user to test the Resource CRUDs
     *
     * @return App\Salon\Worker
     */
    protected function salonAuthUser() : SalonWorker
    {
        return $this->createProfessionalNotAdmin(['salon_id' => $this->salon->id]);
    }

    /**
     * Returns the existing Resource to change on
     * operation CRUD resource
     *
     * @return Employee
     */
    protected function existingResource() : Employee
    {
        // return the auth user itself
        return $this->authUser->makeHidden('salon');
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
        return tap($inputData, function (&$data) {
            // Can't check password. It s generated by a hash cypher and can not be reduplicated
            unset($data['password']);
            // We check here if the salon of the employee is the same as the logged user
            $data['salon_id'] = $this->authUser->salon_id;
            // the is_admin is not fillable
            $data['is_admin'] = $this->authUser->is_admin;
            // the is_professional is not fillable
            $data['is_professional'] = $this->authUser->is_professional;
        });
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
