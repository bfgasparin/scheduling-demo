<?php

namespace Tests\Feature\API\ForSalonProfessional;

use Tests\TestCase;
use App\Salon\Service;
use App\Salon\Employee;
use Illuminate\Support\Collection;
use Tests\Concerns\SalonWorkerHelpers;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Testing Professional managing its Services
 *
 * @see App\Salon\Professional\Service
 * @see App\Http\Controllers\API\Salon\ServiceController
 */
class ManageServicesTest extends TestCase
{
    use DatabaseTransactions,
        SalonWorkerHelpers;

    /**
     * Professional user authenticated which will test the the endpoints
     *
     * @var App\Salon\Worker
     */
    protected $authUser;


    /** @before */
    public function setUpAuthUser() : void
    {
        $this->authUser = $this->createProfessionalNotAdmin();
    }

    /** @before */
    public function decorateCollection() : void
    {
        Collection::macro('input', function () {
            return $this->map(function ($service) {
                return collect(['service_id' => $service->id, 'duration' => $service->duration]);
            })->values();
        });
    }

    /** @test */
    public function a_professional_can_attach_services_to_itself()
    {
        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('POST', "/api/services",
                with($input = factory(Service::class, 3)->create(['salon_id' => $this->authUser->getSalon()->id])->input())->toArray()
            )->assertSuccessful();

        // assert the services was attached to the professional
        $input->each(function($value) {
            $this->assertDatabaseHas(
                'professional_service',
                $value->merge(['professional_id' => $this->authUser->id])->toArray()
            );
        });
    }

    /** @test */
    public function a_professional_can_replates_the_services_when_attach_services_to_itself()
    {
        // Given the professional has some services already
        $this->authUser->attachService(
            with($services = factory(Service::class, 3)->create(['salon_id' => $this->authUser->getSalon()->id])->pluck('id'))
        );

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('POST', "/api/services",
                with($input = factory(Service::class, 3)->create(['salon_id' => $this->authUser->getSalon()->id])->input())->toArray()
            )->assertSuccessful();

        // assert the new services was attached to the professional
        $input->each(function($input) {
            $this->assertDatabaseHas(
                'professional_service',
                $input->merge(['professional_id' => $this->authUser->id])->toArray()
            );
        });

        // assert the old services was detached to the professional
        $services->each(function($id) {
            $this->assertDatabaseMissing(
                'professional_service',
                ['service_id' => $id, 'professional_id' => $this->authUser->id]
            );
        });
    }

    /** @test */
    public function a_professional_can_see_the_services_it_offers()
    {
        // GIven the salon has some services
        $services = factory(Service::class, 10)->create(['salon_id' => $this->authUser->getSalon()->id]);
        // Given the professional offers some of them
        $this->authUser->attachService(with($ids = $services->random(4)->pluck('id')));

        // Authenticate to API
        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('GET', "/api/services")
            ->assertSuccessful()
            ->assertJsonFragment($ids->mapWithKeys(function($id) {
                return ['id' => $id];
            })->toArray());
    }

    /** @test */
    public function a_professional_can_set_its_on_service_duration()
    {
        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('POST', "/api/services",
                with($data = factory(Service::class, 3)->create(['salon_id' => $this->authUser->getSalon()->id])->input())
                    ->transform(function($data) {
                            $data['duration'] *= 2;
                            return $data;
                    })->toArray()
            )->assertSuccessful();

        // assert the professioal services has its onw duration
        $data->each(function($value) {
            $this->assertDatabaseHas(
                'professional_service',
                $value->merge(['professional_id' => $this->authUser->id])->toArray()
            );
        });
    }

    /** @test */
    public function validates_service_id_and_duration_are_required()
    {
        $this->actingAs($this->authUser, 'api-salon-professionals');

        $this->json('POST', "/api/services",
            with($data = factory(Service::class, 3)->create(['salon_id' => $this->authUser->getSalon()->id])->input())
                ->map->only('duration')->toArray()
        )->assertStatus(422)
        ->assertJsonStructure(['0.service_id']);

        $this->json('POST', "/api/services",
            $data->map->only('service_id')->toArray()
        )->assertStatus(422)
        ->assertJsonStructure(['0.duration']);

        // assert the professioal services was not attached
        $data->each(function ($value) {
            $this->assertDatabaseMissing(
                'professional_service',
                $value->merge(['professional_id' => $this->authUser->id])->toArray()
            );
        });
    }

    /** @test */
    public function validates_service_id_exists()
    {
        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('POST', "/api/services",
            with($data = factory(Service::class, 3)->create(['salon_id' => $this->authUser->getSalon()->id])->input())
                ->transform(function($data) {
                        $data['service_id'] *= 2;
                        return $data;
                })->toArray())
            ->assertStatus(422)
            ->assertJsonStructure(['0.service_id', '1.service_id', '2.service_id']);

        // assert the professioal services was not attached
        $data->each(function ($value) {
            $this->assertDatabaseMissing(
                'professional_service',
                $value->merge(['professional_id' => $this->authUser->id])->toArray()
            );
        });
    }

    /** @test */
    public function a_professional_can_not_attach_services_from_another_salon()
    {
        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('POST', "/api/services",
                with($input = factory(Service::class, 3)->create()->input())->toArray()
            )->assertStatus(422)
            ->assertJsonStructure(['0.service_id', '1.service_id', '2.service_id']);

        // assert the professioal services was not attached
        $input->each(function ($value) {
            $this->assertDatabaseMissing(
                'professional_service',
                collect($value)->merge(['professional_id' => $this->authUser->id])->toArray()
            );
        });
    }

    /** @test */
    public function a_professional_can_not_attach_services_to_others_professionals()
    {
        $professional = $this->createProfessionalNotAdmin(['salon_id' => $this->authUser->salon_id]);

        $this->actingAs($this->authUser, 'api-salon-professionals')
            ->json('POST', "/api/professionals/{$professional->id}/services",
                with($input = factory(Service::class, 3)->create(['salon_id' => $this->authUser->salon_id])->input())->toArray()
            )->assertStatus(401);

        // assert the services was not attached to the professional
        $input->each(function ($input) use ($professional) {
            $this->assertDatabaseMissing(
                'professional_service',
                ['service_id' => $input['service_id'], 'professional_id' => $professional->id]
            );
        });
    }
}
