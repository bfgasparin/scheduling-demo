<?php

namespace Tests\Feature\API\Concerns\Validation;

use Illuminate\Support\Str;

/**
 * Contains tests related with email field validation error
 *
 * @see https://laravel.com/docs/validation#rule-email
 */
trait EmailRuleTests
{
    /**
     * @dataProvider emailDataProviderForCreate
     * @test
     */
    public function it_validates_email_rule_on_create(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnCreate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    /**
     * @dataProvider emailDataProviderForUpdate
     * @test
     */
    public function it_validates_email_rule_on_update(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnUpdate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }
    // providers

    public function emailDataProviderForCreate()
    {
        return $this->emailDataProvider(
            $this->getRules($this->emailRules, 'create')
        );
    }

    public function emailDataProviderForUpdate()
    {
        return $this->emailDataProvider(
            $this->getRules($this->emailRules, 'update')
        );
    }

    // providers

    protected function emailDataProvider(array $rules) : array
    {
        return collect($rules)->map(function ($rule) {
            // return the test parameters
            return [
                [$rule, 'invalidemail'],
                [$rule, 'invalid@email'],
                [$rule, 'invalid.email'],
                [$rule, 'invalid@.email'],
                [$rule, '12367890'],
                [$rule,  12934],
                [$rule,  12.32],
                [$rule, Str::random(65).'@email.com'], // to many large email
            ];
        })->collapse()->toArray();
    }
}
