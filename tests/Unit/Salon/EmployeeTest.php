<?php

namespace Tests\Unit\Salon;

use Tests\TestCase;
use App\Salon\Employee;
use Tests\Concerns\SalonWorkerHelpers;
use App\Salon\Professional\WorkingJorney;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Test Salon Employee
 */
class EmployeeTest extends TestCase
{
    use SalonWorkerHelpers,
        DatabaseTransactions;

    /** @test */
    public function professionals_can_have_working_jorneys() : void
    {
        tap($this->createProfessional(), function ($professional) {
            $this->assertNull($professional->workingJorney);
            $this->assertInstanceOf(HasOne::class, $professional->workingJorney());

            with($data = factory(WorkingJorney::class)->make());
            tap($professional->workingJorney()->save($data), function ($workingJorney) {
                $this->assertInstanceOf(WorkingJorney::class, $workingJorney);
            });
        });
    }

    /** @test @expectedException BadMethodCallException */
    public function not_professional_can_not_have_working_jorneys() : void
    {
        tap(factory(Employee::class)->states('not_professional')->create(), function ($admin) {
            $this->assertNull($admin->workingJorney);
            tap($admin->workingJorney(), function () {});
        });
    }
}
