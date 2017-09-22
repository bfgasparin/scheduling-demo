<?php

namespace Tests\Feature\API;

use Tests\TestCase;
use Tests\Feature\API\Concerns\{SimpleCRUDResourceTests, SizeHelpers, DigitsHelpers, HasFakerAttribute};
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Base test class to test CRUD resource operations
 */
abstract class CRUDTestCase extends TestCase
{
    use DatabaseTransactions,
        SimpleCRUDResourceTests,
        SizeHelpers,
        DigitsHelpers,
        HasFakerAttribute;
}
