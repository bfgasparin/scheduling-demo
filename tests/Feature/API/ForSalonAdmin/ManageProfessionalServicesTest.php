<?php

namespace Tests\Feature\API\ForSalonAdmin;

use Tests\TestCase;
use App\Salon\Service;
use App\Salon\Employee;
use Illuminate\Support\Collection;
use Tests\Concerns\SalonWorkerHelpers;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Testing a Admin managing professional's services
 *
 * @see App\Salon\Professional\Service
 */
class ManageProfessionalServicesTest extends TestCase
{
    use DatabaseTransactions,
        SalonWorkerHelpers;

    /**
     * User authenticated which will test the the endpoints
     *
     * @var App\Salon\Worker
     */
    protected $authUser;

    /**
     * The Professional to attach services
     *
     * @var App\Salon\Worker
     */
    protected $professional;

    /** @before */
    public function setUpAuthUser() : void
    {
        $this->authUser = $this->createEmployeeAdmin();
    }

    /** @before */
    public function setUpProfessional() : void
    {
        $this->professional = $this->createProfessionalNotAdmin(['salon_id' => $this->authUser->getSalon()->id]);
    }

    /** @before */
    public function setUpCollectionMacros() : void
    {
        Collection::macro('input', function () {
            return $this->map(function ($service) {
                return collect(['service_id' => $service->id, 'duration' => $service->duration]);
            })->values();
        });
    }

    /** @test */
    public function a_salon_admin_can_attach_services_to_the_professional()
    {
        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('POST', "/api/professionals/{$this->professional->id}/services",
                with($input = factory(Service::class, 3)->create(['salon_id' => $this->authUser->getSalon()->id])->input())->toArray()
            )->assertSuccessful();

        // assert the services was attached to the professional
        $input->each(function($value) {
            $this->assertDatabaseHas(
                'professional_service',
                $value->merge(['professional_id' => $this->professional->id])->toArray()
            );
        });
    }

    /** @test */
    public function a_salon_admin_can_replates_the_services_of_the_professional()
    {
        // Given the professional has some services already
        $this->professional->attachService(
            with($services = factory(Service::class, 3)->create(['salon_id' => $this->authUser->getSalon()->id])->pluck('id'))
        );

        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('POST', "/api/professionals/{$this->professional->id}/services",
                with($input = factory(Service::class, 3)->create(['salon_id' => $this->authUser->getSalon()->id])->input())->toArray()
            )->assertSuccessful();

        // assert the new services was attached to the professional
        $input->each(function($value) {
            $this->assertDatabaseHas(
                'professional_service',
                $value->merge(['professional_id' => $this->professional->id])->toArray()
            );
        });

        // assert the old services was detached to the professional
        $services->each(function($id) {
            $this->assertDatabaseMissing(
                'professional_service',
                ['service_id' => $id, 'professional_id' => $this->professional->id]
            );
        });
    }

    /** @test */
    public function a_salon_admin_can_returns_the_services_of_the_professional()
    {
        // GIven the salon has some services
        $services = factory(Service::class, 10)->create(['salon_id' => $this->authUser->getSalon()->id]);
        // Given the professional offers some of them
        $this->professional->attachService($ids = $services->random(4)->pluck('id'));

        // Authenticate to API
        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('GET', "/api/professionals/{$this->professional->id}/services")
            ->assertSuccessful()
            ->assertJsonFragment($ids->mapWithKeys(function($id) {
                return ['id' => $id];
            })->toArray());
    }

    /** @test */
    public function a_professional_can_only_attach_services_to_himself()
    {
        //Given the salon has some other professional
        $professional = $this->createProfessionalNotAdmin(['salon_id' => $this->authUser->getSalon()->id]);

        $this->actingAs($this->professional, 'api-salon-admins')
            ->json('POST', "/api/professionals/{$professional->id}/services",
                with($input = factory(Service::class, 3)->create(['salon_id' => $this->authUser->getSalon()->id])->input())->toArray()
            )->assertStatus(403);

        // assert the services was not attached to the other professional
        $input->each(function($value) use ($professional) {
            $this->assertDatabaseMissing(
                'professional_service',
                ['service_id' => $value['service_id'], 'professional_id' => $professional->id]
            );
        });
    }

    /** @test */
    public function a_salon_admin_can_not_attach_services_to_another_admin_employee()
    {
        // Given the salon has another admin employee
        $admin = $this->createProfessionalAdmin(['salon_id' => $this->authUser->getSalon()->id]);

        // Authenticate to API
        $this->actingAs($this->authUser, 'api-salon-admins')
           ->json('POST', "/api/professionals/{$admin->id}/services",
                with($input = factory(Service::class, 3)->create(['salon_id' => $this->authUser->getSalon()->id])->input())->toArray()
            )->assertStatus(403);

        // assert the services was not attached to the other admin
        $input->each(function($input) use ($admin) {
            $this->assertDatabaseMissing(
                'professional_service',
                ['service_id' => $input['service_id'], 'professional_id' => $admin->id]
            );
        });
    }

    /** @test */
    public function a_salon_admin_can_not_attach_to_a_professional_from_another_salon()
    {
        // Given we have a professional for another salon
        $professional = factory(Employee::class)->create();

        // Authenticate to API
        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('POST', "/api/professionals/{$professional->id}/services",
                with($input = factory(Service::class, 3)->create(['salon_id' => $this->authUser->getSalon()->id])->input())->toArray()
            )->assertStatus(404);

        // assert the services was not attached to the professional
        $input->each(function($value) use ($professional) {
            $this->assertDatabaseMissing(
                'professional_service',
                ['service_id' => $value['service_id'], 'professional_id' => $professional->id]
            );
        });
    }

    /** @test */
    public function a_salon_admin_can_not_attach_to_a_non_professional_employee()
    {
        // Given we have a non professional professional
        $professional = factory(Employee::class)->states('not_professional')->create(['salon_id' => $this->authUser->getSalon()->id]);

        // Authenticate to API
        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('POST', "/api/professionals/{$professional->id}/services",
                with($input = factory(Service::class, 3)->create(['salon_id' => $this->authUser->getSalon()->id])->input())->toArray()
            )->assertStatus(404);

        // assert the services was not attached to the professional
        $input->each(function($value) use ($professional) {
            $this->assertDatabaseMissing(
                'professional_service',
                ['service_id' => $value['service_id'], 'professional_id' => $professional->id]
            );
        });
    }

    /** @test */
    public function a_professional_can_have_its_on_service_duration()
    {
        // select services to attach
        $existingData = factory(Service::class, 3)->create(['salon_id' => $this->authUser->getSalon()->id])
            ->input();

        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('POST', "/api/professionals/{$this->professional->id}/services",
                with($input = $existingData->transform(function($data) {
                        $data['duration'] *= 2;
                        return $data;
                }))->toArray())
            ->assertSuccessful();


        // assert the professioal services has its onw duration
        $input->each(function($value) {
            $this->assertDatabaseHas(
                'professional_service',
                $value->merge(['professional_id' => $this->professional->id])->toArray()
            );
        });

    }

    /** @test */
    public function validates_service_id_and_duration_are_required()
    {
        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('POST', "/api/professionals/{$this->professional->id}/services",
                with($data = factory(Service::class, 3)->create(['salon_id' => $this->authUser->getSalon()->id])
                    ->input())->map->only('duration')->toArray()
            )->assertStatus(422)
            ->assertJsonStructure(['0.service_id']);

        $this->json('POST',
            "/api/professionals/{$this->professional->id}/services",
            $data->map->only('service_id')->toArray()
        )->assertStatus(422)
        ->assertJsonStructure(['0.duration']);

        // assert the professioal services was not attached
        $data->each(function($value) {
            $this->assertDatabaseMissing(
                'professional_service',
                $value->merge(['professional_id' => $this->professional->id])->toArray()
            );
        });
    }

    /** @test */
    public function validates_service_id_exists()
    {
        // select services to attach

        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('POST', "/api/professionals/{$this->professional->id}/services",
                with($data = factory(Service::class, 3)->create(['salon_id' => $this->authUser->getSalon()->id])
                    ->input())->transform(function($data) {
                        $data['service_id'] *= 2;
                        return $data;
                    })->toArray()
            )->assertStatus(422)
            ->assertJsonStructure(['0.service_id', '1.service_id', '2.service_id']);

        // assert the professioal services was not attached
        $data->each(function($value) {
            $this->assertDatabaseMissing(
                'professional_service',
                $value->merge(['professional_id' => $this->professional->id])->toArray()
            );
        });
    }

    /** @test */
    public function a_salon_admin_can_not_attach_services_from_another_salon()
    {
        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('POST', "/api/professionals/{$this->professional->id}/services",
                with($input = factory(Service::class, 3)->create()->input())->toArray()
            )->assertStatus(422)
            ->assertJsonStructure(['0.service_id', '1.service_id', '2.service_id']);

        // assert the professioal services was not attached
        $input->each(function($value) {
            $this->assertDatabaseMissing(
                'professional_service',
                collect($value)->merge(['professional_id' => $this->professional->id])->toArray()
            );
        });
    }
}
