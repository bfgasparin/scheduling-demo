<?php

namespace Tests\Feature\API\Concerns\Validation;

/**
 * Contains tests that related with require field validation error
 *
 * @see https://laravel.com/docs/validation#rule-required
 */
trait RequiredRuleTests
{
    /**
     * @dataProvider requiredDataProviderForCreate
     * @test
     */
    public function it_validates_required_rule_on_create(string $attribute) : void
    {
        $this->expectsValidationErrorsOnCreate(
            [],
            [$attribute]
        );
    }

    /**
     * @dataProvider requiredDataProviderForUpdate
     * @group a
     * @test
     */
    public function it_validates_required_rule_on_update(string $attribute) : void
    {
        $this->expectsValidationErrorsOnUpdate(
            [],
            [$attribute]
        );
    }

    // providers

    public function requiredDataProviderForCreate()
    {
        return $this->requiredDataProvider(
            $this->getRules($this->requiredRules, 'create')
        );
    }

    public function requiredDataProviderForUpdate()
    {
        return $this->requiredDataProvider(
            $this->getRules($this->requiredRules, 'update')
        );
    }

    protected function requiredDataProvider(array $rules) : array

    {
        return collect($rules)->map(function ($rule) {
            // return the test parameters
            return [
                [$rule],
                [$rule],
                [$rule],
                [$rule],
                [$rule],
            ];
        })->collapse()->toArray();
    }
}
