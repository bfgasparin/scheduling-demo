<?php

namespace Tests\Unit\Salon;

use Tests\TestCase;
use App\Salon\Service;

/**
 * Test Salon Service
 */
class ServiceTest extends TestCase
{
    /** @test */
    public function new_instance_has_default_attributes() : void
    {
        tap(new Service([]), function ($service) {
            $this->assertEquals('always', $service->client_visibility);
        });
    }
}
