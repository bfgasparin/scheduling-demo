<?php

namespace Tests\Feature\API\Concerns\Validation;

use Illuminate\Support\Str;

/**
 * Contains tests related with letters validation errors
 *
 */
trait LettersRuleTests
{
    /**
     * @dataProvider lettersDataProviderForCreate
     * @test
     */
    public function it_validates_letters_rule_on_create(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnCreate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    /**
     * @dataProvider lettersDataProviderForUpdate
     * @test
     */
    public function it_validates_letters_rule_on_update(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnUpdate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    // providers

    public function lettersDataProviderForCreate()
    {
        return $this->lettersDataProvider(
            $this->getRules($this->lettersRules, 'create')
        );
    }

    public function lettersDataProviderForUpdate()
    {
        return $this->lettersDataProvider(
            $this->getRules($this->lettersRules, 'update')
        );
    }

    // providers

    protected function lettersDataProvider(array $rules) : array
    {
        return collect($rules)->map(function ($rule) {
            // return the test parameters
            return [
                [$rule, '123456789'],
                [$rule, '872556282238'],
                [$rule, '36689493.38374'],
                [$rule, '9982653@687436_'],
            ];
        })->collapse()->toArray();
    }
}
