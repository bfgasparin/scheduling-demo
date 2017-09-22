<?php

namespace Tests\Feature\API\Concerns\Validation;

use Illuminate\Support\Collection;

/**
 * Contains tests related with min field validation error
 *
 * @see https://laravel.com/docs/validation#rule-min
 */
trait MinRuleTests
{
    /**
     * @dataProvider minDataProviderForCreate
     * @test
     */
    public function it_validates_min_rule_on_create(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnCreate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    /**
     * @dataProvider minDataProviderForUpdate
     * @test
     */
    public function it_validates_min_rule_on_update(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnUpdate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    // providers

    public function minDataProviderForCreate()
    {
        return $this->minDataProvider(
            $this->getRules($this->minRules, 'create')
        );
    }

    public function minDataProviderForUpdate()
    {
        return $this->minDataProvider(
            $this->getRules($this->minRules, 'update')
        );
    }


    // providers

    protected function minDataProvider(array $rules) : array
    {
        $stringRules = $this->getMinRuleTestDataFor('string', $rules);
        $integerRules = $this->getMinRuleTestDataFor('integer', $rules);
        $numericRules = $this->getMinRuleTestDataFor('numeric', $rules);

        return $integerRules->merge($stringRules)->merge($numericRules)->toArray();
    }

    /**
     * Extract the test data for the given data $type
     *
     * @param string $type
     *
     * @return Collection
     */
    protected function getMinRuleTestDataFor(string $type, array $rules) : Collection
    {
        return collect($rules[$type] ?? [])->map(function ($rule) use ($type) {
            //variables
            $attribute = is_array($rule) ? $rule[0] : $rule; // attribute name
            $size = is_array($rule) ? ($rule[1] ?? 255) : 255; // attribute max size
            $value = $rule[2] ?? $this->makeValueSmallerThan($type, $size);

            // return the test parameters
            return [[$attribute, $value]];
        })->collapse();
    }
}
