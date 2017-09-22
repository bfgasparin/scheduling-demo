<?php

namespace Tests\Feature\API\ForGuest;

use Bus;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Jobs\User\Create as CreateUser;
use Tests\Feature\API\Concerns\{FormatsData, CRUDResourceTestable};
use Tests\Feature\API\Concerns\Validation\{RequiredRuleTests, MaxRuleTests, EmailRuleTests, CaseDiffRuleTests, NumbersRuleTests, StringRuleTests, PhoneRuleTests};

/**
 * Testing a user creating its account
 */
class CreateUserTest extends TestCase
{
    use CRUDResourceTestable,
        DatabaseTransactions,
        FormatsData,
        RequiredRuleTests,
        MaxRuleTests,
        EmailRuleTests,
        CaseDiffRuleTests,
        NumbersRuleTests,
        StringRuleTests,
        PhoneRuleTests;

    /**
     * Class name of the resource model to be used by the Simple CRUD tests
     * @see  Tests\Feature\API\Concerns\CRUDResourceTestable
     */
    protected $model = User::class;

    /**
     * The resource name on uri to be used by the Simple CRUD tests
     *
     * @see Tests\Feature\API\Concerns\CRUDResourceTestable
     */
    protected $resource = 'users';

    /**
     * The table associated with the resource used by the Simple CRUD tests
     *
     * @see Tests\Feature\API\Concerns\CRUDResourceTestable
     */
    protected $table = 'users';

    /**
     * CRUD operations to test for the resource
     *
     * @see Tests\Feature\API\Concerns\Validation\AssertsRules
     */
    protected $operations = ['create'];

    /**
     * The fields that should be tested against the required validation rule
     *
     * @see RequiredRuleTests
     */
    protected $requiredRules = [
        'name', 'email', 'password', 'cellphone'
    ];

    /**
     * The fields that should be tested against the email validation rule
     *
     * @see RequiredRuleTests
     */
    protected $emailRules = [
        'email'
    ];

    /**
     * The fields that should be tested against the max validation rule
     *
     * @see MaxRuleTests
     */
    protected $maxRules = [
       'string' => [['name', 100]],
    ];

    /**
     * The fields that should be tested against the string validation rule
     *
     * @see StringRuleTests
     */
    protected $stringRules = [
        'name',
    ];

    /**
     * The fields that should be tested against the email validation rule
     *
     * @see CaseDiffRuleTests
     */
    protected $caseDiffRules = [
        'password',
    ];

    /**
     * The fields that should be tested against the numbers validation rule
     *
     * @see NumbersRuleTests
     */
    protected $numbersRules = [
        'password',
    ];

    /**
     * The fields that should be tested against the phone validation rule
     *
     * @see PhoneRuleTests
     */
    protected $phoneRules = [
        'mobile' => ['cellphone'],
    ];

    /** @test */
    public function a_user_creates_a_new_account_in_the_system()
    {
        Bus::fake();

        $this->json('POST', "/api/users", with($input = $this->inputData()))
            ->assertSuccessful();

        Bus::assertDispatched(CreateUser::class, function ($job) use ($input) {
            return $job->data === $input;
        });
    }

    // confirms_the_phonenumber_to_complete_the_account_registration_and_login_to_the_system

    /**
     * Returns the input data to use for tests with the resource
     * @see Tests\Feature\API\Concerns\FormatsData
     *
     * @return array
     */
    protected function inputData() : array
    {
        return factory(User::class)->make()
            ->makeVisible('password')
            ->toArray();
    }
}
