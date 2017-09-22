<?php

namespace Tests\Feature\API\Concerns\Validation;

use Illuminate\Support\Str;

/**
 * Contains tests related with case_diff validation errors
 *
 */
trait CaseDiffRuleTests
{
    /**
     * @dataProvider casediffDataProviderForCreate
     * @test
     */
    public function it_validates_casediff_rule_on_create(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnCreate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    /**
     * @dataProvider casediffDataProviderForUpdate
     * @test
     */
    public function it_validates_casediff_rule_on_update(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnUpdate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    // providers

    public function caseDiffDataProviderForCreate()
    {
        return $this->caseDiffDataProvider(
            $this->getRules($this->caseDiffRules, 'create')
        );
    }

    public function caseDiffDataProviderForUpdate()
    {
        return $this->caseDiffDataProvider(
            $this->getRules($this->caseDiffRules, 'update')
        );
    }

    protected function caseDiffDataProvider(array $rules) : array
    {
        return collect($rules)->map(function ($rule) {
            // return the test parameters
            return [
                [$rule, 'invalidcased1ff'],
                [$rule, '1nvalidcasedif7'],
                [$rule, 'invalid.case.d1ff'],
                [$rule, 'invalid@case_d1ff'],
                [$rule, '123456789'],
                [$rule, '872556282238'],
                [$rule, '36689493.38374'],
                [$rule, '9982653@687436_'],
            ];
        })->collapse()->toArray();
    }
}
