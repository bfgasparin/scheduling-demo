<?php

namespace Tests\Feature\API\Concerns\Validation;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Str;
use PHPUnit\Framework\Assert as PHPUnit;

/**
 * Contains helper methods to test resource validation rules
 */
trait AssertsValidation
{
    use FormatsData;

    /**
     * Tests the Resource validations rules for both create and update resource routes
     *
     * @param array $input             The Request input to send to API
     * @param array $fieldsWithError   The expected validation errors returned
     */
    protected function expectsValidationErrors(array $input, array $fieldsWithError) : void
    {
        $this->expectsValidationErrorsOnCreate($input, $fieldsWithError);
        $this->expectsValidationErrorsOnUpdate($input, $fieldsWithError);
    }

    /**
     * Tests the Resource validations rules create resource route
     *
     * @param array $input              The Request input to send to API
     * @param array $fieldsWithError   The fields expected to fail on validation
     */
    protected function expectsValidationErrorsOnCreate(array $input, array $fieldsWithError) : void
    {
        $this->setUpApplicationForTestOn('create')
            ->json('POST', "/api/{$this->getCRUDResource()}", $input)
            ->assertStatus(422)
            ->assertJsonFragment($fieldsWithError);

        $this->assertAppAfterValidationOn('create', $input, function ($input) {
            if(!empty($input)) {
                $this->assertDatabaseMissing($this->getCRUDTable(), $this->getDatabaseMissingDataAfterCreateFailure($input));
            }
        });
    }

    /**
     * Tests the Resource validations rules for update resource route
     *
     * @param array $input             The Request input to send to API
     * @param array $fieldsWithError   The fields expected to fail on validation
     */
    protected function expectsValidationErrorsOnUpdate(array $input, array $fieldsWithError) : void
    {
        // set up application before test
        $this->setUpApplicationForTestOn('update');
        $model = $this->getExistingResource();

        $this->json('PUT', "/api/{$this->getCRUDResource()}/{$model->id}", $input)
            ->assertStatus(422)
            ->assertJsonStructure($fieldsWithError);

        $this->assertAppAfterValidationOn('update', $input, function ($input) {
            if(!empty($input)) {
                $this->assertDatabaseMissing($this->getCRUDTable(), $this->getDatabaseMissingDataAfterUpdateFailure($input));
            }
        });
    }

    /**
     * Get the displayable name of the attribute.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function getDisplayableAttribute($attribute) : string
    {
        return str_replace('_', ' ', Str::snake($attribute));
    }

    /**
     * Search for a custom assertion function in the class to assert the App after the validation
     * test. The custom assertion function should be a method in the class with the name  following
     * "assertAppAfter{$operation}Failure" pattern.
     *
     * If not found, the defaultAssertions is called.
     *
     * @param $operation
     * @param $input       The request data sent to the API
     * @param $defaultAssertions  The default assertion method if no custom assertion is defined in the class
     *
     * @return void
     */
    protected function assertAppAfterValidationOn(string $operation, array $input, callable $defaultAssertions) : void
    {
        $operation = Str::studly($operation);
        $assertMethod = "assertAppAfter{$operation}Failure";

        call_user_func(
            method_exists($this, $assertMethod) ? [$this, $assertMethod] : $defaultAssertions,
            $input
        );
    }
}
