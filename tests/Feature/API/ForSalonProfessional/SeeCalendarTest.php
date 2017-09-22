<?php

namespace Tests\Feature\API\ForSalonProfessional;

use App\Salon;
use Carbon\Carbon;
use Tests\TestCase;
use App\Salon\Employee;
use Tests\Feature\API\Concerns\SalonProfessionalCalendarTests;

/**
 * Testing an Profesional seeing its calendar
 */
class SeeCalendarTest extends TestCase
{
    use SalonProfessionalCalendarTests;

    /**
     * The authGuard used to login the user booking the service
     *
     * @var string
a    */
    protected $authGuard = 'api-salon-professionals';

    /**
     * Setup the application before run the tests
     *
     * @return self
     */
    public function setUpAppBeforeTest(Employee $professional) : void
    {
        $this->actingAs($professional, $this->authGuard);
    }

    /** @test */
    public function a_professional_can_not_see_its_calendar_using_professionals_route() : void
    {
        $authUser = $this->createProfessionalNotAdmin();

        $this->actingAs($authUser, $this->authGuard)
            ->json('GET', "/api/professionals/{$authUser->id}/calendar")
            ->assertStatus(401);
    }

    /** @test */
    public function a_professional_can_not_see_calendar_of_another_professional() : void
    {
        $authUser = $this->createProfessionalNotAdmin();
        $professional = $this->createProfessionalNotAdmin();

        $professional = $this->createProfessionalWithWorkingJorney([
            'days_of_week' => [Carbon::today()->dayOfWeek]
        ]);

        $this->actingAs($authUser, $this->authGuard)
            ->json('GET', "/api/professionals/{$professional->id}/calendar")
            ->assertStatus(401);
    }

    /** @test */
    public function a_professional_can_not_see_calendar_of_professional_of_another_salon() : void
    {
        $authUser = $this->createProfessionalNotAdmin();
        $professional = $this->createProfessionalWithWorkingJorney([
            'days_of_week' => [Carbon::today()->dayOfWeek]
        ]);

        // Insert the professional to another salon
        factory(Salon::class)->create()->employees()->save($professional);

        $this->actingAs($authUser, $this->authGuard)
            ->json('GET', "/api/professionals/{$professional->id}/calendar")
            ->assertStatus(401);
    }

    public function a_professional_can_see_the_salon_calendar() : void
    {
        $this->markTestSkipped("Not implemented yet");
    }

    /**
     * Returns the calendar url to test
     * @see Tests\Feature\API\Concerns\SalonProfessionalCalendarTests
     *
     * @return string
     */
    protected function url() : string
    {
        return "calendar";
    }
}
