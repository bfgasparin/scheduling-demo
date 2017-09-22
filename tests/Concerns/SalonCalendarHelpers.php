<?php

namespace Tests\Concerns;

use App\Salon;
use App\Salon\Client\Booking;
use Tests\Concerns\SalonWorkerHelpers;
use App\Salon\Client\BookingCollection;
use App\Salon\Professional\WorkingJorney;
use App\Salon\{Client, Employee, Service};
use App\Salon\Professional\WorkingJorney\Schedule;

/**
 * Contains helper functions for tests related to Salon Calendar
 *
 * @see App\Salon\Calendar
 */
trait SalonCalendarHelpers
{
    use SalonWorkerHelpers;

    /**
     * The salon to be used on tests
     * @var Salon
     */
    protected $salon;

    /** @before */
    protected function setUpSalon() : void
    {
        $this->salon = tap($this->salon(), function ($salon) {
            $salon->services()->createMany(
                factory(Service::class, 25)->states('always_visible_for_guests')->create()->toArray());
        });
    }

    /**
     * Returns a Salon to be used on test
     *
     * @return App\Salon
     */
    protected function salon() : Salon
    {
        return factory(Salon::class)->create();
    }

    /**
     * Create a random Professional with WorkingJorney using the given $workingJorneyData
     *
     * @param array $workingJorneyData  The professional WorkingJorney attributes
     *
     * @return App\Salon\Employee
     */
    protected function createProfessionalWithWorkingJorney(array $workingJorneyData = []) : Employee
    {
        $professional = $this->createProfessionalNotAdmin(['salon_id' => $this->salon->id]);

        return tap($professional, function ($professional) use ($workingJorneyData) {
            $professional->attachService($this->salon->services->random(rand(1, 15)));
            $professional->workingJorney()->save(factory(WorkingJorney::class)->create($workingJorneyData));
        });
    }

    /**
     * Create a random Professional for the given workingJorney data and with schedules
     * for each instance in $datesEntriesAndExits array
     *
     * Each instance in given $datesEntriesAndExits is a schedule with the given date, entry
     * and exit for the created professional
     *
     * @param array $workingJorneyData
     * @param array $datesEntriesAndExits
     *
     * @return App\Salon\Employee
     */
    protected function createProfessionalWithSchedules(array $workingJorneyData, array $datesEntriesAndExits) : Employee
    {
        $professional = $this->createProfessionalWithWorkingJorney($workingJorneyData);

        return tap($professional, function ($professional) use ($datesEntriesAndExits) {
            foreach ($datesEntriesAndExits as [$date, $entry, $exit]) {
                $professional->workingJorney->schedules()->save(factory(Schedule::class)->make([
                    'date' => $date,
                    'entry' => $entry,
                    'exit' => $exit,
                ]));
            }
        });
    }

    /**
     * Create random Bookings for the given professional for each instance in $datesAndIntervals array
     *
     * Each instance in given $datesAndIntervals is a booking with the given date and interval
     *
     * @param Employee $professional
     * @param array $datesAndIntervals
     *
     * @return App\Salon\Client\BookingCollection
     */
    protected function createBookingsForProfessional(Employee $professional, array $datesAndIntervals) : BookingCollection
    {
        return tap($professional, function ($professional) use ($datesAndIntervals) {
            foreach ($datesAndIntervals as [$date, $interval]) {
                factory(Booking::class)->create([
                    'client_id' => factory(Client::class)->create([
                        'salon_id' => $this->salon->id
                    ])->id,
                    'professional_id' => $professional->id,
                    'service_id' => $professional->services->first()->id,
                    'date' => $date,
                    'start' => $interval,
                ]);
            }
        })->bookings;
    }
}
