<?php

namespace Tests\Feature\API\ForSalonAdmin;

use App\Salon;
use Tests\TestCase;
use App\Salon\Employee;
use Tests\Feature\API\CRUDTestCase;
use App\Salon\Worker as SalonWorker;
use Tests\Concerns\SalonWorkerHelpers;
use Illuminate\Database\Eloquent\Model;
use App\Salon\Professional\WorkingJorney;
use Tests\Feature\API\Concerns\SalonWorkerCRUDTests;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\API\Concerns\Validation\{RequiredRuleTests, DateFormatRuleTests};

/**
 * Testing Admin managing Professional's Working Jorney
 *
 * @see WorkingJorney
 */
class ManageProfessionalWorkingJorneysTest extends CRUDTestCase
{
    use SalonWorkerHelpers,
        SalonWorkerCRUDTests,
        RequiredRuleTests,
        DateFormatRuleTests;

    /**
     * Class name of the resource model to be used by the Simple CRUD tests
     * @see SimpleCRUDResourceTests
     */
    protected $model = WorkingJorney::class;

    /**
     * The table associated with the resource used by the Simple CRUD tests
     *
     * @see SimpleCRUDResourceTests
     */
    protected $table = 'professional_working_jorneys';

    /**
     * CRUD operations to test for the resource
     *
     * @see SimpleCRUDResourceTests
     */
    protected $operations = ['create', 'update', 'read'];

    /**
     * The fields that should be tested against the required validation rule
     *
     * @see RequiredRuleTests
     */
    protected $requiredRules = [
        'entry', 'exit', 'lunch', 'days_of_week'
    ];

    /**
     * The fields that should be tested against the date_format validation rule
     *
     * @see DateFormatRuleTests
     */
    protected $dateFormatRules = [
        ['entry', 'H:i:s'],
        ['lunch', 'H:i:s'],
        ['exit', 'H:i:s'],
    ];

    /**
     * The Professional to attach services
     *
     * @var App\Salon\Worker
     */
    protected $professional;

    /** @before */
    public function setUpProfessional() : void
    {
        $this->professional = $this->createProfessionalNotAdmin();
    }

    /**
     * @test
     * @see Tests\Feature\API\Concerns\{SimpleCRUDResourceTests
     * */
    public function it_list_the_resource()
    {
        $this->markTestSkipped("The professional can have only one working jorney, so there is no list of working jorneys.");
    }

    /** @test */
    public function a_salon_admin_can_saves_the_working_jorney_of_the_professional() : void
    {
        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('POST', "/api/professionals/{$this->professional->id}/workingJorneys",
                with($input = collect(factory(WorkingJorney::class)->make()->makeHidden('professional_id')))->toArray()
            )->assertStatus(201)
            ->assertJson($this->getResponseData($input->toArray()));

        // assert the the working jorney was saved
        $this->assertDatabaseHas(
            'professional_working_jorneys',
            $input->put('days_of_week', json_encode($input->get('days_of_week')))
                ->merge(['professional_id' => $this->professional->id])->toArray()
        );
    }

    /** @test */
    public function a_salon_admin_can_see_the_working_jorney_of_a_professional() : void
    {
        // Given the professional has a Working Jorney
        $workingJorney = $this->professional->workingJorney()->save(factory(WorkingJorney::class)->make());

        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('GET', "/api/professionals/{$this->professional->id}/workingJorneys/{$workingJorney->id}")
            ->assertSuccessful()
            ->assertJsonFragment($workingJorney->toArray());
    }

    /** @test */
    public function a_salon_admin_can_not_save_a_working_jorney_on_a_non_professional() : void
    {
        $employee = factory(Employee::class)->states('not_professional')->create(['salon_id' => $this->authUser->getSalon()->id]);


        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('POST', "/api/professionals/{$employee->id}/workingJorneys",
                with($input = collect(factory(WorkingJorney::class)->make()))->toArray()
            )->assertStatus(404);

        // assert the the working jorney was not saved to the
            $this->assertDatabaseMissing(
                'professional_working_jorneys',
                $input->put('days_of_week', json_encode($input->get('days_of_week')))
                    ->merge(['professional_id' => $employee->id])->toArray()
            );
    }

    /**
     * @test
     * @see Tests\Feature\API\Concerns\SalonWorkerCRUDTests
     */
    public function it_can_not_list_resources_from_another_salon()
    {
        // Given we have a professional from other salon
        $professional = with($workingJorney = factory(WorkingJorney::class)->create())->professional;

       // There is not list action for working Jorney, so we test the show action instead
        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('GET', "/api/professionals/{$professional->id}/workingJorneys/{$workingJorney->id}")
            ->assertStatus(404);
    }

    /** @test */
    public function a_salon_admin_can_not_see_a_working_jorney_of_a_professional_using_id_of_another()
    {
        // Given some other professional
        $professional = $this->createProfessional(['salon_id' => $this->professional->salon_id]);

        // And some working Jorney
        $workingJorney = factory(WorkingJorney::class)->create(['professional_id' => $this->professional->id]);

        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('GET', "/api/professionals/{$professional->id}/workingJorneys/{$workingJorney->id}")
            ->assertStatus(404);
    }

    /** @todo other tests
     *  validation days_of_week tests
     *  */

    /**
     * Returns the authenticated user to test the Resource CRUDs
     *
     * @see Tests\Feature\API\Concerns\SalonWorkerCRUDTests
     * @return App\Salon\Worker
     */
    protected function salonAuthUser() : SalonWorker
    {
        return $this->createEmployeeAdmin(['salon_id' => $this->salon->id]);
    }

    /**
     * Returns a Salon to be usedon test the Resource CRUDs
     *
     * @see Tests\Feature\API\Concerns\SalonWorkerCRUDTests
     *
     * @return Salon
     */
    public function salon() : Salon
    {
        return $this->professional->salon;
    }

    /**
     * Returns the name of the resource to test the CRUDs
     *
     * @see Tests\Feature\API\Concerns\CRUDResourceTestable
     * @see Tests\Feature\API\Concerns\SimpleCRUDResourceTests
     *
     * @return string
     */
    protected function getCRUDResource() : string
    {
        return "professionals/{$this->professional->id}/workingJorneys";
    }

    /**
     * Returns the existing Resource to change on
     * operation CRUD resource
     *
     * @return App\Salon\Professional\WorkingJorney
     */
    protected function existingResource() : WorkingJorney
    {
        return factory(WorkingJorney::class)->create([
            'professional_id' => $this->professional->id
        ]);
    }

    /**
     * Returns the data to expect to be into database after the insert resource test success
     *
     * @return array
     */
    protected function databaseData(array $inputData) : array
    {
        return tap($inputData, function (&$data) {
            $data['professional_id'] = $this->professional->id;
            $data['days_of_week']  = json_encode($data['days_of_week']);
        });
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
            $data['professional_id'] = $this->professional->id;
        });
    }

    /**
     * Returns data to expect into database after the update resource test success
     *
     * @param array $inputData The request input data
     * @return array
     */
    protected function databaseDataAfterUpdate(array $inputData) : array
    {
        return tap($inputData, function (&$data) {
            $data['professional_id'] = $this->professional->id;
            $data['days_of_week']  = json_encode($data['days_of_week']);
        });
    }

    /**
     * Returns the data to expect to be into database after failure on validation rule
     * create test
     *
     * @see Tests\Feature\API\Concerns\SalonWorkerCRUDTests
     *
     * @param array $inputData  The request input data
     * @return array
     */
    protected function databaseMissingDataAfterCreateFailure(array $inputData) : array
    {
        return $this->toDatabaseData($inputData);
    }

    /**
     * Returns the data to expect to be missing into database after failure on validation rule
     * update test
     *
     * @see Tests\Feature\API\Concerns\SalonWorkerCRUDTests
     *
     * @param array $inputData  The request input data
     * @return array
     */
    protected function databaseMissingDataAfterUpdateFailure(array $inputData) : array
    {
        return $this->toDatabaseData($inputData);
    }

    /**
     * Returns the data to expect to be into database after error on validation rule
     * update test
     *
     * @see Tests\Feature\API\Concerns\SalonWorkerCRUDTests
     *
     * @param Model $model The existing model
     * @return array
     */
    protected function databaseDataAfterUpdateFailure(Model $model) : array
    {
        return array_merge(
            $model->toArray(),
            ['days_of_week' => json_encode($model->days_of_week)]
        );
    }

    protected function toDatabaseData(array $data) : array
    {
        return tap($data, function (&$data) {
            $data['professional_id'] = $this->professional->id;
            $data['days_of_week']  = json_encode($data['days_of_week']);
        });
    }
}
