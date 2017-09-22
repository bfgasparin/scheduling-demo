<?php

namespace Tests\Feature\API\Concerns\Validation;

use Illuminate\Support\Str;

/**
 * Contains tests related with numbers validation errors
 *
 */
trait NumbersRuleTests
{
    /**
     * @dataProvider numbersDataProviderForCreate
     * @test
     */
    public function it_validates_numbers_rule_on_create(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnCreate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    /**
     * @dataProvider numbersDataProviderForUpdate
     * @test
     */
    public function it_validates_numbers_rule_on_update(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnUpdate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    // providers

    public function numbersDataProviderForCreate()
    {
        return $this->numbersDataProvider(
            $this->getRules($this->numbersRules, 'create')
        );
    }

    public function numbersDataProviderForUpdate()
    {
        return $this->numbersDataProvider(
            $this->getRules($this->numbersRules, 'update')
        );
    }

    protected function numbersDataProvider(array $rules) : array
    {
        return collect($rules)->map(function ($rule) {
            // return the test parameters
            return [
                [$rule, 'Passwordwithoutnumbers'],
                [$rule, 'PasswordWithoutNumbers'],
                [$rule, 'PasswordWithout.numbers'],
                [$rule, 'PasswordWithout@Numbers_'],
                [$rule, 'passwordWithout@Numbers_'],
            ];
        })->collapse()->toArray();
    }
}
