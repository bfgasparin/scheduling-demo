<?php

namespace Tests\Feature\API\ForGuest;

use App\Salon;
use carbon\Carbon;
use Tests\TestCase;
use App\Salon\Employee;
use Tests\Feature\API\Concerns\SalonProfessionalCalendarTests;

/**
 * Testing a non authenticated user seeing a salon professional calendar
 */
class SeeSalonProfessionalCalendarTest extends TestCase
{
    use SalonProfessionalCalendarTests;

    /** @test */
    public function a_guest_can_not_see_a_professional_calendar_using_another_salon() : void
    {
        $today = Carbon::today();
        $professional = $this->createProfessionalWithWorkingJorney([
            'calendar_interval' => 15,
            'entry' => '09:00:00',
            'exit' => '14:30:00',
            'days_of_week' => [$today->tomorrow()->dayOfWeek, $today->nextWeekendDay()->dayOfWeek],
        ]);

        // use another salon
        $anotherSalon = factory(Salon::class)->create();

        $this->json('GET', "/api/salons/{$anotherSalon->id}/professionals/{$professional->id}/calendar")
            ->assertStatus(404);
    }

    /**
     * Returns the calendar url of the given professional
     * @see Tests\Feature\API\Concerns\SalonProfessionalCalendarTests
     *
     * @param App\Salon\Employee $professional
     *
     * @return string
     */
    protected function url(Employee $professional) : string
    {
        return "salons/{$professional->salon_id}/professionals/{$professional->id}/calendar";
    }
}
