<?php

namespace Tests\Feature\API\Concerns\Validation;

use Illuminate\Support\Str;

/**
 * Contains tests related with numeric field validation error
 *
 * @see https://laravel.com/docs/validation#rule-numeric
 */
trait NumericRuleTests
{
    /**
     * @dataProvider numericDataProviderForCreate
     * @test
     */
    public function it_validates_numeric_rule_on_create(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnCreate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    /**
     * @dataProvider numericDataProviderForUpdate
     * @test
     */
    public function it_validates_numeric_rule_on_update(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnUpdate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    // providers

    public function numericDataProviderForCreate()
    {
        return $this->numericDataProvider(
            $this->getRules($this->numericRules, 'create')
        );
    }

    public function numericDataProviderForUpdate()
    {
        return $this->numericDataProvider(
            $this->getRules($this->numericRules, 'update')
        );
    }

    // providers

    protected function numericDataProvider(array $rules) : array
    {
        return collect($rules)->map(function ($rule) {
            // return the test parameters
            return [
                [$rule, 'nonnumeric'],
                [$rule, '1.2a'],
                [$rule, '1a'],
                [$rule, '@$%^&#@('],
            ];
        })->collapse()->toArray();
    }
}
