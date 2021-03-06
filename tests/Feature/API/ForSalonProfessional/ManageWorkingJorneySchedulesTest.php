<?php

namespace Tests\Feature\API\ForSalonProfessional;

use App\Salon;
use App\Salon\Employee;
use Tests\Feature\API\CRUDTestCase;
use App\Salon\Worker as SalonWorker;
use Tests\Concerns\SalonWorkerHelpers;
use Illuminate\Database\Eloquent\Model;
use App\Salon\Professional\WorkingJorney;
use App\Salon\Professional\WorkingJorney\Schedule;
use Tests\Feature\API\Concerns\SalonWorkerCRUDTests;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\API\Concerns\Validation\{RequiredRuleTests, DateRuleTests, DateFormatRuleTests};

/*
 * Testing Professional managing its WorkingJorney's Schedules
 *
 * @see App\Salon\Worker
 * @see App\Salon\Professional\WorkingJorney\Schedule
 * @see App\Http\Controllers\API\Salon\Professional\WorkingJorneyScheduleController
 */
class ManageWorkingJorneySchedulesTest extends CRUDTestCase
{
    use SalonWorkerHelpers,
        SalonWorkerCRUDTests,
        RequiredRuleTests,
        DateRuleTests,
        DateFormatRuleTests;

    /**
     * Class name of the resource model to be used by the Simple CRUD tests
     * @see SimpleCRUDResourceTests
     */
    protected $model = Schedule::class;

    /**
     * The table associated with the resource used by the Simple CRUD tests
     *
     * @see SimpleCRUDResourceTests
     */
    protected $table = 'working_jorney_schedules';

    /**
     * CRUD operations to test for the resource
     *
     * @see SimpleCRUDResourceTests
     */
    protected $operations = ['create', 'update', 'read', 'delete'];

    /**
     * Indicates if the resource has softDelete
     *
     * @see SimpleCRUDResourceTests
     */
    protected $softDeletes = false;

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
        'date', 'entry', 'exit'
    ];

    /**
     * The fields that should be tested against the date validation rule
     *
     * @see DateRuleTests
     */
    protected $dateRules = [
        'date'
    ];

    /**
     * The fields that should be tested against the date_format validation rule
     *
     * @see DateFormatRuleTests
     */
    protected $dateFormatRules = [
        ['entry', 'H:i:s'],
        ['exit', 'H:i:s'],
    ];

    /**
     * The WorkingJorney to register the schedules
     *
     * @var App\Salon\Professional\WorkingJorney
     */
    protected $workingJorney;

    /** @before */
    public function setUpWorkingJorney() : void
    {
        $this->workingJorney = factory(WorkingJorney::class)->create([
            'professional_id' => $this->createProfessionalNotAdmin()->id
        ]);
    }


    /**
     * @test
     * @see Tests\Feature\API\Concerns\SalonWorkerCRUDTests
     */
    public function it_can_not_list_resources_from_another_salon()
    {
        // Given we have a schedules from a professional of other salon
        $workingJorney = with($schedule = factory(Schedule::class)->create())->workingJorney;

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('GET', "/api/workingJorneys/{$workingJorney->id}/schedules/{$schedule->id}")
            ->assertStatus(404);
    }

    /**
     * @test
     * @see Tests\Feature\API\Concerns\SalonWorkerCRUDTests
     */
    public function a_professional_can_not_see_the_resource_from_another_salon()
    {
        // Given we have a schedule from a professional of other salon
        $workingJorney = tap(factory(WorkingJorney::class)->create(), function ($workingJorney) {
            $workingJorney->schedules()->saveMany(factory(Schedule::class, 5)->make());
        });

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('GET', "/api/workingJorneys/{$workingJorney->id}/schedules")
            ->assertStatus(404);
    }

    /** @test */
    public function a_professional_can_list_its_schedules()
    {
        // Given we have a bunch of schedules for the professional
        $workingJorney = tap($this->workingJorney, function ($workingJorney) {
            $workingJorney->schedules()->saveMany(factory(Schedule::class, 5)->make());
        });

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('GET', "/api/workingJorneys/{$workingJorney->id}/schedules")
            ->assertSuccessful()
            ->assertJsonPagination(
                $workingJorney->schedules->forPage(1,15)->toArray()
            );
    }

    /** @test */
    public function a_professional_can_not_see_a_schedule_of_another_professional()
    {
        // Given we have an schedule from another professional
        $schedule = $this->scheduleOfAnotherProfessional();

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('GET', "/api/workingJorneys/{$schedule->workingJorney->id}/schedules/{$schedule->id}")
            ->assertStatus(403);
    }

    /** @test */
    public function a_professional_can_not_see_a_schedule_that_belongs_another_workingJorney()
    {
        // Given we have an schedule from another professional
        $schedule = $this->scheduleOfAnotherProfessional();

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('GET', "/api/workingJorneys/{$this->workingJorney->id}/schedules/{$schedule->id}")
            ->assertStatus(404);
    }

    /** @test */
    public function a_professional_can_not_register_a_schedule_of_another_professional()
    {
        $schedule = $this->scheduleOfAnotherProfessional();

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('POST', "/api/workingJorneys/{$schedule->workingJorney->id}/schedules",
                with($input = $this->inputData()))
            ->assertStatus(403);

        $this->assertDatabaseMissing(
            'working_jorney_schedules',
            tap($input, function (&$data) use ($schedule) {
                $data['working_jorney_id'] = $schedule->workingJorney->id;
                unset($data['day_of_week']);
            })
        );
    }

    /** @test */
    public function a_professional_can_not_update_a_schedule_of_another_professional()
    {
        $schedule = $this->scheduleOfAnotherProfessional();

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('PUT', "/api/workingJorneys/{$schedule->workingJorney->id}/schedules/{$schedule->id}",
                with($input = $this->inputData()))
            ->assertStatus(403);

        $this->assertDatabaseHas(
            'working_jorney_schedules',
            $schedule->makeHidden(['workingJorney', 'day_of_week'])->toArray()
        )->assertDatabaseMissing(
            'working_jorney_schedules',
            tap($input, function (&$data) use ($schedule) {
                $data['working_jorney_id'] = $schedule->workingJorney->id;
                unset($data['day_of_week']);
            })
        );
    }

    /** @test */
    public function a_professional_can_not_delete_a_schedule_of_another_professional()
    {
        $schedule = $this->scheduleOfAnotherProfessional();

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('DELETE', "/api/workingJorneys/{$schedule->workingJorney->id}/schedules/{$schedule->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas(
            'working_jorney_schedules',
            $schedule->makeHidden(['workingJorney', 'day_of_week'])->toArray()
        );
    }

    /**
     * Returns the name of the resource to test the CRUDs
     *
     * @see CRUDResourceTestable
     * @see SimpleCRUDResourceTests
     *
     * @return string
     */
    protected function getCRUDResource() : string
    {
        return "workingJorneys/{$this->workingJorney->id}/schedules";
    }

    /**
     * Returns the authenticated user to test the Resource CRUDs
     *
     * @return App\Salon\Worker
     */
    protected function salonAuthUser() : SalonWorker
    {
        return $this->workingJorney->professional;
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
        return $this->workingJorney->professional->salon;
    }

    /**
     * Returns the existing Resource to change on
     * operation CRUD resource
     *
     * @return Schedule
     */
    protected function existingResource() : Schedule
    {
        return factory(Schedule::class)->create([
            'working_jorney_id' => $this->workingJorney->id
        ]);
    }

    /**
     * Returns the input data to use for tests with the resource
     * @see Tests\Feature\API\CRUDTestCase
     *
     * @return array
     */
    protected function inputData() : array
    {
        // get a Schedule data with a WorkingJorney of a professionakl for the tested salon
        return factory(Schedule::class)->make([
            'working_jorney_id' => $this->workingJorney,
        ])->toArray();
    }

    /**
     * Returns the data to expect to be into database after the insert resource test success
     *
     * @return array
     */
    protected function databaseData(array $inputData) : array
    {
        return $this->toDatabaseData($inputData);
    }

    /**
     * Returns data to expect into database after the update resource test success
     *
     * @param array $inputData The request input data
     * @return array
     */
    protected function databaseDataAfterUpdate(array $inputData) : array
    {
        return $this->toDatabaseData($inputData);
    }

    /**
     * Returns the data to expect to contains on response after the resource create or update test success
     *
     * @param array $inputData  The request input data
     * @return array
     */
    protected function responseData(array $inputData) : array
    {
        return tap($inputData, function(&$data) {
            $data['working_jorney_id'] = $this->workingJorney->id;
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
        return $model->makeHidden('day_of_week')->toArray();
    }

    /**
     * Returns the data to expect to be missing into database after delete the resource
     *
     * @param Model  $model The resource deleted
     * @return array
     */
    protected function getDatabaseMissingDataAfterDelete(Model $model) : array
    {
        return $model->makeHidden('day_of_week')->toArray();
    }

    /**
     * Returns the data to expect to be into database after error on deleting the resource
     *
     * @param Model  $model The resource was tried to delete
     * @return array
     */
    protected function getDatabaseDataAfterDeleteFailure(Model $model) : array
    {
        return $model->makeHidden('day_of_week')->toArray();
    }

    protected function toDatabaseData(array $data) : array
    {
        return tap($data, function (&$data) {
            unset($data['day_of_week']);
            $data['working_jorney_id'] = $this->workingJorney->id;
        });
    }

    /**
     * Returns a Schedule from another Professional
     *
     * @return Schedule
     */
    protected function scheduleOfAnotherProfessional() : Schedule
    {
        $professional = $this->createProfessional(['salon_id' => $this->authUser->salon_id]);

        return tap(factory(Schedule::class)->create(), function ($schedule) use ($professional) {
            $schedule->workingJorney->professional()->associate($professional);
            $schedule->workingJorney->save();
        });
    }
}
