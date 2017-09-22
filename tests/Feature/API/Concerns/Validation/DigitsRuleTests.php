<?php

namespace Tests\Feature\API\Concerns\Validation;

use Illuminate\Support\Collection;

/**
 * Contains tests related with digits field validation error
 *
 * @see https://laravel.com/docs/validation#rule-digits
 */
trait DigitsRuleTests
{
    /**
     * @dataProvider digitsDataProviderForCreate
     * @test
     */
    public function it_validates_digits_rule_on_create(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnCreate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    /**
     * @dataProvider digitsDataProviderForUpdate
     * @test
     */
    public function it_validates_digits_rule_on_update(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnUpdate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    // providers

    public function digitsDataProviderForCreate()
    {
        return $this->digitsDataProvider(
            $this->getRules($this->digitsRules, 'create')
        );
    }

    public function digitsDataProviderForUpdate()
    {
        return $this->digitsDataProvider(
            $this->getRules($this->digitsRules, 'update')
        );
    }

    // providers

    protected function digitsDataProvider(array $rules) : array
    {
        return collect($rules)->map(function ($rule) {
            //variables
            $attribute = is_array($rule) ? $rule[0] : $rule; // attribute name
            $size = is_array($rule) ? ($rule[1] ?? 255) : 255; // attribute digits size

            // return the test parameters
            return [
                [$attribute, $this->makeValueWithDigitsGreaterThan('integer', $size)],
                [$attribute, $this->makeValueWithDigitsSmallerThan('integer', $size)],
                [$attribute, $this->makeValueWithDigitsGreaterThan('numeric', $size)],
                [$attribute, $this->makeValueWithDigitsSmallerThan('numeric', $size)],
                [$attribute, $this->makeValueWithDigitsGreaterThan('string', $size)],
                [$attribute, $this->makeValueWithDigitsSmallerThan('string', $size)],
            ];
        })->collapse()->toArray();
    }
}
