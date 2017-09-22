<?php

use Faker\Generator as FakerGenerator;

/**
 * Helper methodos to interact with Faker Generator
 *
 * @see Faker\Generator
 */
trait InteractsWithFaker
{
    /**
     * Reset unique flags of Faker Providers
     *
     * @return void
     */
    public function resetFakerUniqueFlags() : void
    {
        tap($this->container->make(FakerGenerator::class))->unique(true);
    }
}
