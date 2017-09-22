<?php

namespace Tests\Concerns;

use App\Salon\Employee;

/**
 * Contains helper functions for tests using Salon Worker implementations
 *
 * @see App\Salon\Worker
 */
trait SalonWorkerHelpers
{
    /**
     * Create a new professional using the given attributes
     *
     * @param array $attributes
     *
     * @return App\Salon\Employee
     */
    protected function createProfessional(array $attributes = []) : Employee
    {
        return factory(Employee::class)->states('professional')->create($attributes);
    }

    /**
     * Create a new professional which is also a salon admin using the given parameters
     *
     * @param array $attributes
     */
    protected function createProfessionalAdmin(array $attributes = []) : Employee
    {
        return factory(Employee::class)->states('professional', 'admin')->create($attributes);
    }

    /**
     * Create a new professional which is is not a salon admin using the given parameters
     *
     * @param array $attributes
     */
    protected function createProfessionalNotAdmin(array $attributes = []) : Employee
    {
        return factory(Employee::class)->states('professional', 'not_admin')->create($attributes);
    }

    /**
     * Create a salon employee admin using the given parameters
     *
     * @param array $attributes
     */
    protected function createEmployeeAdmin(array $attributes = []) : Employee
    {
        return factory(Employee::class)->states('admin')->create($attributes);
    }

    /**
     * Create a salon admin which is not a professional using the given parameters
     *
     * @param array $attributes
     */
    protected function createAdminNotProfessional(array $attributes = []) : Employee
    {
        return factory(Employee::class)->states('admin', 'not_professional')->create($attributes);
    }

    /**
     * Create a salon employee which is not a professional using the given parameters
     *
     * @param array $attributes
     */
    protected function createEmployeeNotProfessional(array $attributes = []) : Employee
    {
        return factory(Employee::class)->states('not_professional')->create($attributes);
    }

    /**
     * Create a salon employee which is not a salon admin using the given parameters
     *
     * @param array $attributes
     */
    protected function createEmployeeNotAdmin(array $attributes = []) : Employee
    {
        return factory(Employee::class)->states('not_admin')->create($attributes);
    }
}
