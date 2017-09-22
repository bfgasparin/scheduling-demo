<?php

namespace Tests\Feature\API\ForGuest;

use App\Salon;
use Tests\TestCase;
use App\Salon\Employee;
use Illuminate\Support\Collection;
use Tests\Concerns\SalonWorkerHelpers;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Testing a non authenticated user seeing a salon's professionals
 */
class SeeSalonProfessionalsTest extends TestCase
{
    use DatabaseTransactions,
        SalonWorkerHelpers;

    /** @test */
    public function a_guest_can_see_a_professional_of_a_salon() : void
    {
        // fixtures
        $professional = $this->createProfessional();

        $this->json('GET', "/api/salons/{$professional->salon_id}/professionals/{$professional->id}")
            ->assertSuccessful()
            ->assertJson($professional->toArray());

    }

    /** @test */
    public function a_guest_can_see_a_non_professional_of_a_salon() : void
    {
        // fixtures
        $employee = factory(Employee::class)->states('not_professional')->create();

        $this->json('GET', "/api/salons/{$employee->salon_id}/professionals/{$employee->id}")
            ->assertStatus(404);
    }

    /** @test */
    public function a_guest_can_not_see_professional_of_a_salon_using_another_salon() : void
    {
        // fixtures
        $professional = $this->createProfessional();
        $otherProfessional = $this->createProfessional();

        $this->json('GET', "/api/salons/{$professional->salon_id}/professionals/{$otherProfessional->id}")
            ->assertStatus(404);
    }

    /** @test */
    public function a_guest_can_list_all_professionals_of_a_salon() : void
    {
        // create professionals for random salons
        repeat(10, function () {
            $this->createProfessional();
        });

        // get a salo
        $salon = factory(Salon::class)->create();

        // create professionals for salon
        $professionals = Collection::times(30, function () use ($salon) {
            return $this->createProfessional(['salon_id' => $salon->id]);
        });

        $this->json('GET', "/api/salons/{$salon->id}/professionals")
            ->assertSuccessful()
            ->assertJsonPagination(
                $professionals->forPage(1,15)->toArray(),
                30
            );
    }

    /** @test */
    public function a_guest_can_not_list_professional_of_a_salon_using_another_salon() : void
    {
        // create professionals for random salons
        repeat(5, function () {
            $this->createProfessional();
        });

        // get a salon
        $salon = factory(Salon::class)->create();

        // create professionals for salon
        $professionals = Collection::times(20, function () use ($salon) {
            return $this->createProfessional(['salon_id' => $salon->id]);
        });
        factory(Employee::class, 5)->states('not_professional')->create(['salon_id' => $salon->id]); // create professionals for salon

        $this->json('GET', "/api/salons/{$salon->id}/professionals")
            ->assertSuccessful()
            ->assertJsonPagination(
                $professionals->forPage(1, 15)->toArray(),
                20
            );
    }
}
