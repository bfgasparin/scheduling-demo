<?php

namespace Tests\Feature\API\Concerns;

use Faker\Generator as FakerGenerator;

/**
 * Add faker generator attribute to test class
 */
trait HasFakerAttribute
{
    /**
     * The Faker Generator to generate fake data for testing
     *
     * @var Faker\Generator
     */
    protected $faker;

    /**
     * @before
     */
    protected function setUpFakerGenerator()
    {
        $this->faker = app(FakerGenerator::class);
    }
}
