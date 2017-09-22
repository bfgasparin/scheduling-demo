<?php

namespace Tests\Feature\API\Concerns\Validation;

use Illuminate\Support\Collection;

/**
 * Contains tests related with max field validation error
 *
 * @see https://laravel.com/docs/validation#rule-max
 */
trait MaxRuleTests
{
    /**
     * @dataProvider maxDataProviderForCreate
     * @test
     */
    public function it_validates_max_rule_on_create(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnCreate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    /**
     * @dataProvider maxDataProviderForUpdate
     * @test
     */
    public function it_validates_max_rule_on_update(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnUpdate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    // providers

    public function maxDataProviderForCreate()
    {
        return $this->maxDataProvider(
            $this->getRules($this->maxRules, 'create')
        );
    }

    public function maxDataProviderForUpdate()
    {
        return $this->maxDataProvider(
            $this->getRules($this->maxRules, 'update')
        );
    }

    // providers

    protected function maxDataProvider(array $rules) : array
    {
        $stringRules = $this->getMaxRuleTestDataFor('string', $rules);
        $integerRules = $this->getMaxRuleTestDataFor('integer', $rules);
        $numericRules = $this->getMaxRuleTestDataFor('numeric', $rules);

        return $integerRules->merge($stringRules)->merge($numericRules)->toArray();
    }

    /**
     * Extract the test data for the given data $type
     *
     * @param string $type
     *
     * @return Collection
     */
    protected function getMaxRuleTestDataFor(string $type, array $rules) : Collection
    {
        return collect($rules[$type] ?? [])->map(function ($rule) use ($type) {
            //variables
            $attribute = is_array($rule) ? $rule[0] : $rule; // attribute name
            $size = is_array($rule) ? ($rule[1] ?? 255) : 255; // attribute max size
            $value = $rule[2] ?? $this->makeValueGreaterThan($type, $size);

            // return the test parameters
            return [[$attribute, $value]];
        })->collapse();
    }
}
