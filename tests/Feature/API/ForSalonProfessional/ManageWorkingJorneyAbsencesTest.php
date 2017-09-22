<?php

namespace Tests\Feature\API\ForSalonProfessional;

use App\Salon;
use App\Salon\Employee;
use Tests\Feature\API\CRUDTestCase;
use App\Salon\Worker as SalonWorker;
use Tests\Concerns\SalonWorkerHelpers;
use Illuminate\Database\Eloquent\Model;
use App\Salon\Professional\WorkingJorney;
use App\Salon\Professional\WorkingJorney\Absence;
use Tests\Feature\API\Concerns\SalonWorkerCRUDTests;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\API\Concerns\Validation\{RequiredRuleTests, DateRuleTests, StringRuleTests};

/*
 * Testing Professional managing its WorkingJorney's Absences
 *
 * @see App\Salon\Worker
 * @see App\Salon\Professional\WorkingJorney\Absence
 * @see App\Http\Controllers\API\Salon\Professional\WorkingJorneyAbsenceController
 */
class ManageWorkingJorneyAbsencesTest extends CRUDTestCase
{
    use SalonWorkerHelpers,
        SalonWorkerCRUDTests,
        RequiredRuleTests,
        DateRuleTests,
        StringRuleTests;

    /**
     * Class name of the resource model to be used by the Simple CRUD tests
     * @see SimpleCRUDResourceTests
     */
    protected $model = Absence::class;

    /**
     * The table associated with the resource used by the Simple CRUD tests
     *
     * @see SimpleCRUDResourceTests
     */
    protected $table = 'working_jorney_absences';

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
        'date', 'observation'
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
     * The fields that should be tested against the string validation rule
     *
     * @see StringRuleTests
     */
    protected $stringRules = [
        'observation'
    ];


    /**
     * The WorkingJorney to register the absences
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
    public function a_professional_can_not_see_resources_from_another_salon()
    {
        // Given we have a absences from a professional of other salon
        $workingJorney = with($absence = factory(Absence::class)->create())->workingJorney;

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('GET', "/api/workingJorneys/{$workingJorney->id}/absences/{$absence->id}")
            ->assertStatus(404);
    }

    /** @test */
    public function a_professional_can_list_its_absences()
    {
        // Given we have a bunch of absences for the professional
        $workingJorney = tap($this->workingJorney, function ($workingJorney) {
            $workingJorney->absences()->saveMany(factory(Absence::class, 5)->make());
        });

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('GET', "/api/workingJorneys/{$workingJorney->id}/absences")
            ->assertSuccessful()
            ->assertJsonPagination(
                $workingJorney->absences->forPage(1,15)->toArray()
            );
    }

    /**
     * @test
     * @see Tests\Feature\API\Concerns\SalonWorkerCRUDTests
     */
    public function it_can_not_list_resources_from_another_salon()
    {
        // Given we have a absence from a professional of other salon
        $workingJorney = tap(factory(WorkingJorney::class)->create(), function ($workingJorney) {
            $workingJorney->absences()->saveMany(factory(Absence::class, 5)->make());
        });

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('GET', "/api/workingJorneys/{$workingJorney->id}/absences")
            ->assertStatus(404);
    }

    /** @test */
    public function a_professional_can_not_see_a_absence_of_another_professional()
    {
        // Given we have an absence from another professional
        $absence = $this->absenceOfAnotherProfessional();

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('GET', "/api/workingJorneys/{$absence->workingJorney->id}/absences/{$absence->id}")
            ->assertStatus(403);
    }

    /** @test */
    public function a_professional_can_not_see_a_absence_that_belongs_another_workingJorney()
    {
        // Given we have an absence from another professional
        $absence = $this->absenceOfAnotherProfessional();

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('GET', "/api/workingJorneys/{$this->workingJorney->id}/absences/{$absence->id}")
            ->assertStatus(404);
    }

    /** @test */
    public function a_professional_can_not_register_a_absence_of_another_professional()
    {
        $absence = $this->absenceOfAnotherProfessional();

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('POST', "/api/workingJorneys/{$absence->workingJorney->id}/absences",
                with($input = $this->inputData()))
            ->assertStatus(403);

        $this->assertDatabaseMissing('working_jorney_absences',
            tap($input, function (&$data) use ($absence) {
                $data['working_jorney_id'] = $absence->workingJorney->id;
                unset($data['day_of_week']);
            })
        );
    }

    /** @test */
    public function a_professional_can_not_update_a_absence_of_another_professional()
    {
        $absence = $this->absenceOfAnotherProfessional();

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('PUT', "/api/workingJorneys/{$absence->workingJorney->id}/absences/{$absence->id}",
                with($input = $this->inputData()))
            ->assertStatus(403);

        $this->assertDatabaseHas(
            'working_jorney_absences',
            $absence->makeHidden(['workingJorney', 'day_of_week'])->toArray()
        )->assertDatabaseMissing(
            'working_jorney_absences',
            tap($input, function (&$data) use ($absence) {
                $data['working_jorney_id'] = $absence->workingJorney->id;
                unset($data['day_of_week']);
            })
        );
    }

    /** @test */
    public function a_professional_can_not_delete_a_absence_of_another_professional()
    {
        $absence = $this->absenceOfAnotherProfessional();

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('DELETE', "/api/workingJorneys/{$absence->workingJorney->id}/absences/{$absence->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas(
            'working_jorney_absences',
            $absence->makeHidden(['workingJorney', 'day_of_week'])->toArray()
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
        return "workingJorneys/{$this->workingJorney->id}/absences";
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
     * @return Absence
     */
    protected function existingResource() : Absence
    {
        return factory(Absence::class)->create([
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
        // get an Absence data with a WorkingJorney of a professionakl for the tested salon
        return factory(Absence::class)->make([
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
     * Returns a Absence from another Professional
     *
     * @return Absence
     */
    protected function absenceOfAnotherProfessional() : Absence
    {
        $professional = $this->createProfessional(['salon_id' => $this->authUser->salon_id]);

        return tap(factory(Absence::class)->create(),
            function ($absence) use ($professional) {
                $absence->workingJorney->professional()->associate($professional);
                $absence->workingJorney->save();
            });
    }
}
