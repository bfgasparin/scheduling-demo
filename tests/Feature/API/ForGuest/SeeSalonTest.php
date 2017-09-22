<?php

namespace Tests\Feature\API\ForGuest;

use App\Salon;
use Tests\Feature\API\CRUDTestCase;
use Tests\Feature\API\Concerns\Validation\{RequiredRuleTests, MaxRuleTests, StringRuleTests, InRuleTests, EmailRuleTests};

/**
 * Testing non authenticated user seeing the salons
 */
class SeeSalonTest extends CRUDTestCase
{
    use RequiredRuleTests,
        MaxRuleTests,
        StringRuleTests,
        InRuleTests,
        EmailRuleTests;

    /**
     * Class name of the resource model to be used
     * by the Simple CRUD tests
     *
     * @see SimpleCRUDResourceTests
     */
    protected $model = Salon::class;

    /**
     * The resource name on uri to be used by the Simple CRUD tests
     *
     * @see SimpleCRUDResourceTests
     */
    protected $resource = 'salons';

    /**
     * The table associated with the resource used by the Simple CRUD tests
     *
     * @see SimpleCRUDResourceTests
     */
    protected $table = 'salons';

    /**
     * CRUD operations to test for the resource
     *
     * @see SimpleCRUDResourceTests
     */
    protected $operations = ['create', 'update', 'delete', 'read'];

    /**
     * The fields that should be tested against the required validation rule
     *
     * @see RequiredRuleTests
     */
    protected $requiredRules = [
        'name', 'description', 'category', 'email'
    ];

    /**
     * The fields that should be tested against the max validation rule
     *
     * @see MaxRuleTests
     */
    protected $maxRules = [
        'string' => [['name', '100'], ['description', 255]],
    ];

    /**
     * The fields that should be tested against the string validation rule
     *
     * @see StringRuleTests
     */
    protected $stringRules = [
        'name', 'description', 'category',
    ];

    /**
     * The fields that should be tested against the in validation rule
     *
     * @see InRuleTests
     */
    protected $inRules = [
        'category',
    ];

    /**
     * The fields that should be tested against the email validation rule
     *
     * @see RequiredRuleTests
     */
    protected $emailRules = [
        'email'
    ];

    /** @test */
    public function anyone_can_list_the_salons() : void
    {
        // fixtures
        $salons = factory(Salon::class, 30)->create();

        $this->json('GET', "/api/salons")
            ->assertSuccessful()
            ->assertJsonPagination(
                $salons->forPage(1, 15)->toArray(),
                30
            );
    }

}
