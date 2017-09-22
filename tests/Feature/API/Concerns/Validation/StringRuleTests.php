<?php

namespace Tests\Feature\API\Concerns\Validation;

use Illuminate\Support\Str;

/**
 * Contains tests related with string field validation error
 *
 * @see https://laravel.com/docs/validation#rule-string
 */
trait StringRuleTests
{
    /**
     * @dataProvider stringDataProviderForCreate
     * @test
     */
    public function it_validates_string_rule_on_create(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnCreate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    /**
     * @dataProvider stringDataProviderForUpdate
     * @test
     */
    public function it_validates_string_rule_on_update(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnUpdate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    // providers

    public function stringDataProviderForCreate()
    {
        return $this->stringDataProvider(
            $this->getRules($this->stringRules, 'create')
        );
    }

    public function stringDataProviderForUpdate()
    {
        return $this->stringDataProvider(
            $this->getRules($this->stringRules, 'update')
        );
    }

    // providers

    protected function stringDataProvider(array $rules) : array
    {
        return collect($rules)->map(function ($rule) {
            // return the test parameters
            return [
                [$rule, ''],
                [$rule, null],
                [$rule, 9],
                [$rule, 287463],
                [$rule, 433.1265],
            ];
        })->collapse()->toArray();
    }
}
