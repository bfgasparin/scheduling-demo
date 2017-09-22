<?php

namespace Tests\Feature\API\ForGuest;

use App\Salon;
use Tests\TestCase;
use App\Salon\Service;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Testing a non authenticated user seeing a salon's services
 */
class SeeSalonServicesTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_guest_can_see_a_service_of_a_salon() : void
    {
        // fixtures
        $service = factory(Service::class)->states('always_visible_for_guests')->create();

       $this->json('GET', "/api/salons/{$service->salon_id}/services/{$service->id}")
            ->assertSuccessful()
            ->assertJson($service->toArray());

    }

    /** @test */
    public function a_guest_can_not_see_service_of_a_salon_using_another_salon() : void
    {
        // fixtures
        $service = factory(Service::class)->states('always_visible_for_guests')->create();
        $otherService = factory(Service::class)->states('always_visible_for_guests')->create();

        $this->json('GET', "/api/salons/{$service->salon_id}/services/{$otherService->id}")
            ->assertStatus(404);
    }

    /** @test */
    public function a_guest_can_not_see_a_service_of_a_salon_that_is_never_visible_for_guests() : void
    {
        // fixtures
        $service = factory(Service::class)->states('never_visible_for_guests')->create();

       $this->json('GET', "/api/salons/{$service->salon_id}/services/{$service->id}")
            ->assertStatus(404);

    }

    /** @test */
    public function a_guest_can_list_only_services_of_a_salon_always_visible_for_guests() : void
    {
        // fixtures
        $salon = factory(Salon::class)->create(); // get a salon
        factory(Service::class, 4)->states('never_visible_for_guests')->create(['salon_id' => $salon->id]);
        $visibleServices = factory(Service::class, 9)->states('always_visible_for_guests')
            ->create(['salon_id' => $salon->id]);

        $this->json('GET', "/api/salons/{$salon->id}/services")
            ->assertSuccessful()
            ->assertJsonPagination(
                $visibleServices->forPage(1,15)->toArray(),
                9
            );
    }
}
