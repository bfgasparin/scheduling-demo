<?php

namespace Tests\Feature\API\Concerns\Validation;

use Illuminate\Support\Str;

/**
 * Contains tests related with integer field validation error
 *
 * @see https://laravel.com/docs/validation#rule-integer
 */
trait IntegerRuleTests
{
    /**
     * @dataProvider integerDataProviderForCreate
     * @test
     */
    public function it_validates_integer_rule_on_create(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnCreate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    /**
     * @dataProvider integerDataProviderForUpdate
     * @test
     */
    public function it_validates_integer_rule_on_update(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnUpdate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    // providers

    public function integerDataProviderForCreate()
    {
        return $this->integerDataProvider(
            $this->getRules($this->integerRules, 'create')
        );
    }

    public function integerDataProviderForUpdate()
    {
        return $this->integerDataProvider(
            $this->getRules($this->integerRules, 'update')
        );
    }

    // providers

    protected function integerDataProvider(array $rules) : array
    {
        return collect($rules)->map(function ($rule) {
            // return the test parameters
            return [
                [$rule, 'noninteger'],
                [$rule, '1.2'],
                [$rule, '1a'],
                [$rule, '@$%^&#@('],
                [$rule, 2.4],
            ];
        })->collapse()->toArray();
    }
}
