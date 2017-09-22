<?php

namespace Tests;

use Tests\Concerns\CatchesException;
use Tests\Feature\API\Concerns\AssertsResponse;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Feature\API\Concerns\Validation\{AssertsValidation, AssertsRules };

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication,
        AssertsValidation,
        AssertsRules,
        AssertsResponse,
        CatchesException;
}
