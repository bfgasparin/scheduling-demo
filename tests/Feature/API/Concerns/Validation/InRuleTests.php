<?php

namespace Tests\Feature\API\Concerns\Validation;

use Illuminate\Support\Str;

/**
 * Contains tests related with in field validation error
 * In fact it just test the field against a random string value
 *
 * @see https://laravel.com/docs/validation#rule-in
 */
trait InRuleTests
{
    /**
     * @dataProvider inDataProviderForCreate
     * @test
     */
    public function it_validates_in_rule_on_create(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnCreate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    /**
     * @dataProvider inDataProviderForUpdate
     * @test
     */
    public function it_validates_in_rule_on_update(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnUpdate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    // providers

    public function inDataProviderForCreate()
    {
        return $this->inDataProvider(
            $this->getRules($this->inRules, 'create')
        );
    }

    public function inDataProviderForUpdate()
    {
        return $this->inDataProvider(
            $this->getRules($this->inRules, 'update')
        );
    }

    // providers

    protected function inDataProvider(array $rules) : array
    {
        return collect($rules)->map(function ($rule) {
            // return the test parameters
            return [
                [$rule, Str::quickRandom()],
            ];
        })->collapse()->toArray();
    }
}
