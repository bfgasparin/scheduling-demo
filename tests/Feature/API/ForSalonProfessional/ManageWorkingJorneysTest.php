<?php

namespace Tests\Feature\API\ForSalonProfessional;

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
use Tests\Feature\API\Concerns\Validation\{
    RequiredRuleTests,
    DateFormatRuleTests,
    IntegerRuleTests,
    InRuleTests
};

/*
 * Testing Professional managing its WorkingJorney
 *
 * @see App\Salon\Worker
 * @see App\Salon\Professional\WorkingJorney
 * @see App\Http\Controllers\API\Salon\Professional\WorkingJorneyController
 */
class ManageWorkingJorneysTest extends CRUDTestCase
{
    use SalonWorkerHelpers,
        SalonWorkerCRUDTests,
        RequiredRuleTests,
        DateFormatRuleTests,
        IntegerRuleTests,
        InRuleTests;

    /**
     * Class name of the resource model to be used by the Simple CRUD tests
     * @see SimpleCRUDResourceTests
     */
    protected $model = WorkingJorney::class;

    /**
     * The resource name on uri to be used by the Simple CRUD tests
     *
     * @see SimpleCRUDResourceTests
     */
    protected $resource = 'workingJorney';

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
        'entry', 'exit', 'lunch', 'days_of_week', 'calendar_interval'
    ];

    /**
     * The fields that should be tested against the integer validation rule
     *
     * @see IntegerRuleTests
     */
    protected $integerRules = [
        'calendar_interval',
    ];

    /**
     * The fields that should be tested against the in validation rule
     *
     * @see InRuleTests
     */
    protected $inRules = [
        'calendar_interval',
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

    /** @test */
    public function a_professional_can_save_its_working_jorney() : void
    {
        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('POST', "/api/workingJorney",
                with($input = collect(factory(WorkingJorney::class)->make()->makeHidden('professional_id')))->toArray())
            ->assertStatus(201);

        // assert the the working jorney was saved
        $this->assertDatabaseHas(
            'professional_working_jorneys',
            $input->put('days_of_week', json_encode($input['days_of_week']))
                ->merge(['professional_id' => $this->authUser->id])->toArray()
        );
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
    public function a_professional_can_see_its_working_jorney() : void
    {
        // Given the auth user has a working Jorney
        $workingJorney = factory(WorkingJorney::class)->create(['professional_id' => $this->authUser->id]);

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('GET', "/api/workingJorney/{$workingJorney->id}")
            ->assertSuccessful()
            ->assertJsonFragment($workingJorney->toArray());
    }

    /** @test */
    public function a_professional_can_not_save_working_jorney_of_others_professionals() : void
    {
        // Given we have a another professional
        $professional = $this->createProfessional(['salon_id' => $this->authUser->salon_id]);

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('POST', "/api/professionals/{$professional->id}/workingJorneys",
                with($input = collect(factory(WorkingJorney::class)->make()->makeHidden('professional_id')))->toArray()
            )->assertStatus(401);

        // assert the the working jorney was not saved
        $this->assertDatabaseMissing(
            'professional_working_jorneys',
            $input->put('days_of_week', json_encode($input['days_of_week']))
                ->merge(['professional_id' => $professional->id])->toArray()
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
        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('GET', "/api/professionals/{$professional->id}/workingJorneys/{$workingJorney->id}")
            ->assertStatus(401);
    }

    /** @test */
    public function a_professional_can_not_see_working_jorney_of_another_professional()
    {
        // Given we have a professional from other salon
        $workingJorney = $this->workingJorneyOfAnotherProfessional();

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('GET', "/api/professionals/{$workingJorney->professional->id}/workingJorneys/{$workingJorney->id}")
            ->assertStatus(401);
    }

    /** @test */
    public function a_professional_can_not_update_a_working_jorney_of_another_professional()
    {
        // Given we have a professional from other salon
        $workingJorney = $this->workingJorneyOfAnotherProfessional();

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('PUT', "/api/professionals/{$workingJorney->professional->id}/workingJorneys/{$workingJorney->id}",
                with($input = collect(factory(WorkingJorney::class)->make()->makeHidden('professional_id')))->toArray())
            ->assertStatus(401);

        // assert the the working jorney was not saved
        $this->assertDatabaseMissing(
            'professional_working_jorneys',
            $input->put('days_of_week', json_encode($input['days_of_week']))
                ->merge(['professional_id' => $workingJorney->professional->id])->toArray()
        );
    }

    /** @todo other tests
     *  validation days_of_week tests
     *  */

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
     * @return WorkingJorney
     */
    protected function existingResource() : WorkingJorney
    {
        return factory(WorkingJorney::class)->create([
            'professional_id' => $this->authUser->id
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
            $data['professional_id'] = $this->authUser->id;
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
            $data['professional_id'] = $this->authUser->id;
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
            $data['professional_id'] = $this->authUser->id;
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
            $data['professional_id'] = $this->authUser->id;
            $data['days_of_week']  = json_encode($data['days_of_week']);
        });
    }

    /**
     * Returns a WorkingJorney from another Professional
     *
     * @return WorkingJorney
     */
    protected function workingJorneyOfAnotherProfessional() : WorkingJorney
    {
        return tap(factory(WorkingJorney::class)->create(), function ($workingJorney) {
            $workingJorney->professional()->associate(
                $this->createProfessional(
                    ['salon_id' => $this->authUser->salon_id]
                )
            );
        });

    }
}
