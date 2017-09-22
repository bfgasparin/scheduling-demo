<?php

namespace Tests\Feature\API\Concerns\Validation;

use Illuminate\Support\Collection;

/**
 * Contains tests related with between field validation error
 *
 * @see https://laravel.com/docs/validation#rule-between
 */
trait BetweenRuleTests
{
    /**
     * @dataProvider betweenDataProviderForCreate
     * @test
     */
    public function it_validates_between_rule_on_create(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnCreate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    /**
     * @dataProvider betweenDataProviderForUpdate
     * @test
     */
    public function it_validates_between_rule_on_update(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnUpdate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    // providers

    public function betweenDataProviderForCreate()
    {
        return $this->betweenDataProvider(
            $this->getRules($this->betweenRules, 'create')
        );
    }

    public function betweenDataProviderForUpdate()
    {
        return $this->betweenDataProvider(
            $this->getRules($this->betweenRules, 'update')
        );
    }

    // providers

    protected function betweenDataProvider(array $rules) : array
    {
        $stringRules = $this->getBetweenRuleTestDataFor('string', $rules);
        $integerRules = $this->getBetweenRuleTestDataFor('integer', $rules);
        $numericRules = $this->getBetweenRuleTestDataFor('numeric', $rules);

        return $integerRules->merge($stringRules)->merge($numericRules)->toArray();
    }

    /**
     * Extract the test data for the given data $type
     *
     * @param string $type
     *
     * @return Collection
     */
    protected function getBetweenRuleTestDataFor(string $type, array $rules) : Collection
    {
        return collect($rules[$type] ?? [])->map(function ($rule) use ($type) {
            //variables
            $attribute = is_array($rule) ? $rule[0] : $rule; // attribute name
            $minSize = is_array($rule) ? ($rule[1] ?? 5) : 5; // attribute min size
            $maxSize = is_array($rule) ? ($rule[2] ?? 255) : 255; // attribute max size
            $minValue = $rule[3] ?? $this->makeValueSmallerThan($type, $minSize);
            $maxValue = $rule[4] ?? $this->makeValueGreaterThan($type, $maxSize);

            // return the test parameters
            return [
                [$attribute, $minValue],
                [$attribute, $maxValue],
            ];
        })->collapse();
    }
}
