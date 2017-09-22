<?php

namespace Tests\Feature\Salon\Admin;

use App\Salon;
use Carbon\Carbon;
use Tests\TestCase;
use App\Salon\Employee;
use Tests\Feature\API\Concerns\SalonProfessionalCalendarTests;

/**
 * Testing an Admin seeing calendar of professionals
 */
class SeeProfessionalCalendarTest extends TestCase
{
    use SalonProfessionalCalendarTests;

    /**
     * The authGuard used to login the user booking the service
     *
     * @var string
     */
    protected $authGuard = 'api-salon-admins';

    /**
     * User authenticated which will test the the endpoints
     *
     * @var App\Salon\Worker
     */
    protected $authUser;

    /** @before */
    public function setUpAuthUser() : void
    {
        $this->authUser = $this->createEmployeeAdmin();
    }

    /**
     * Returns a Salon to be used on test
     * @see Tests\Concerns\SalonCalendarHelpers
     *
     * @return App\Salon
     */
    public function salon() : Salon
    {
        return $this->authUser->salon;
    }

    /**
     * Setup the application before run the tests
     *
     * @return self
     */
    public function setUpAppBeforeTest() : void
    {
        $this->actingAs($this->authUser, $this->authGuard);
    }

    /** @test */
    public function a_salon_admin_can_not_see_calendar_of_professional_from_another_salon() : void
    {
        $professional = $this->createProfessionalWithWorkingJorney([
            'days_of_week' => [Carbon::today()->dayOfWeek]
        ]);

        // Insert the professional to another salon
        factory(Salon::class)->create()->employees()->save($professional);

        // setup application before test
        $this->setUpApplication($professional);

        $this->json('GET', "/api/{$this->urlFor($professional)}")
            ->assertStatus(404);
    }

    /** @test */
    public function a_salon_admin_can_not_see_calendar_of_professional_from_another_salon_on_the_given_date() : void
    {
        $date = Carbon::today()->addDays(rand(1,30));
        $professional = $this->createProfessionalWithWorkingJorney([
            'days_of_week' => [$date->dayOfWeek]
        ]);

        // Insert the professional to another salon
        factory(Salon::class)->create()->employees()->save($professional);

        // setup application before test
        $this->setUpApplication($professional);

        $this->json('GET', "/api/{$this->urlFor($professional)}?date={$date->toDateString()}")
            ->assertStatus(404);
    }

    /** @test */
    public function a_salon_admin_can_not_see_calendar_of_professional_from_another_salon_on_a_range_of_date() : void
    {
        $date = Carbon::today()->addDay(rand(1,30));
        $endDate = $date->addDays(rand(1,5));
        $professional = $this->createProfessionalWithWorkingJorney([
            'days_of_week' => [$date->dayOfWeek]
        ]);

        // Insert the professional to another salon
        factory(Salon::class)->create()->employees()->save($professional);

        // setup application before test
        $this->setUpApplication($professional);

        $this->json('GET', "/api/{$this->urlFor($professional)}?range={$date->toDateString()},{$endDate->toDateString()}")
            ->assertStatus(404);
    }
}
