<?php

namespace Tests\Feature\API\Concerns\Validation;

use RuntimeException;

/**
 * Contains tests that related with exists validation errors
 *
 * @see https://laravel.com/docs/validation#rule-exists
 */
trait ExistsRuleTests
{
    /**
     * @dataProvider existsDataProviderForCreate
     * @test
     */
    public function it_validates_exists_rule_on_create(string $attribute) : void
    {
        $inputData = $this->getInputData();
        if (!isset($inputData[$attribute])){
            throw new RuntimeException("The '{$attribute}' attribute is not a valid field for the model");
        }
        $inputData[$attribute] += 2;

        $this->expectsValidationErrorsOnCreate(
            $inputData,
            [$attribute]
        );
    }

    /**
     * @dataProvider existsDataProviderForUpdate
     * @test
     */
    public function it_validates_exists_rule_on_update(string $attribute) : void
    {
        $inputData = $this->getInputData();
        if (!isset($inputData[$attribute])){
            throw new RuntimeException("The '{$attribute}' attribute is not a valid field for the model");
        }
        $inputData[$attribute] += 2;

        $this->expectsValidationErrorsOnUpdate(
            $inputData,
            [$attribute]
        );
    }

    // providers

    public function existsDataProviderForCreate()
    {
        return $this->existsDataProvider(
            $this->getRules($this->existsRules, 'create')
        );
    }

    public function existsDataProviderForUpdate()
    {
        return $this->existsDataProvider(
            $this->getRules($this->existsRules, 'update')
        );
    }

    // providers

    protected function existsDataProvider(array $rules) : array
    {
        return collect($rules)->map(function ($attribute) {
            // return the test parameters
            return [
                [$attribute],
            ];
        })->collapse()->toArray();
    }
}
