<?php

namespace Tests\Feature\API\Salon;

use App\Salon;
use Tests\TestCase;
use App\Salon\Employee;
use Illuminate\Support\Collection;
use Tests\Concerns\SalonWorkerHelpers;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Testing Admin managing Professional in the salon
 */
class ManageProfessionalsTest extends TestCase
{
    use DatabaseTransactions,
        SalonWorkerHelpers;

    /**
     * User authenticated which will test the the endpoints
     *
     * @var App\Salon\Worker
     */
    protected $authUser;


    /** @before */
    public function setUpAuthUser() : void
    {
        $this->authUser = $this->createAdminNotProfessional();
    }

    public function a_salon_admin_can_see_a_professinal() : void
    /** @test */
    {
        // fixtures
        $professional = $this->createProfessional(['salon_id' => $this->authUser->salon_id]);

        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('GET', "/api/professionals/{$professional->id}")
            ->assertSuccessful()
            ->assertJson($professional->toArray());
    }

    /** @test */
    public function a_salon_admin_can_not_see_a_non_professional() : void
    {
        // fixtures
        $employee = factory(Employee::class)->states('not_professional')->create([
            'salon_id' => $this->authUser->salon_id
        ]);

        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('GET', "/api/salons/{$employee->salon_id}/professionals/{$employee->id}")
            ->assertStatus(404);
    }

    /** @test */
    public function a_salon_admin_can_not_see_professional_of_another_salon() : void
    {
        $professional = $this->createProfessional();

        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('GET', "/api/professionals/{$professional->id}")
            ->assertStatus(404);
    }

    /** @test */
    public function a_salon_admin_can_list_all_professionals() : void
    {
        // create professionals for random salons
        repeat(10, function () {
            $this->createProfessional();
        });

        // create professionals for the same salon as the auth user
        $professionals = Collection::times(20, function () {
            return $this->createProfessional([
                'salon_id' => $this->authUser->salon_id
            ]);
        });

        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('GET', "/api/professionals")
            ->assertSuccessful()
            ->assertJsonPagination(
                $professionals->forPage(1,15)->toArray(),
                20
            );
    }

    /** @test */
    public function a_salon_admin_can_list_only_the_professionals() : void
    {
        // create professionals for random salons
        repeat(10, function () {
            $this->createProfessional();
        });

        // create professionals for salon
        $professionals = Collection::times(20, function () {
            return $this->createProfessional([
                'salon_id' => $this->authUser->salon_id
            ]);
        });

        // create employees not professionals for salon
        repeat(5, function () {
            $this->createEmployeeNotProfessional([
                'salon_id' => $this->authUser->salon_id
            ]);
        });

        $this->actingAs($this->authUser, 'api-salon-admins')
            ->json('GET', "/api/professionals")
            ->assertSuccessful()
            ->assertJsonPagination(
                $professionals->forPage(1, 15)->toArray(),
                20
            );
    }
}
