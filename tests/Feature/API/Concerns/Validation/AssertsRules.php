<?php

namespace Tests\Feature\API\Concerns\Validation;

use Illuminate\Support\Str;

/**
 * Halper to validate an attribute
 */
trait AssertsRules
{
    protected function validateAttributeField($attribute, $value, array $errors) : void
    {
        $this->expectsValidationErrors(
            $this->makeInputDataUsingAttribute($attribute, $value),
            $errors
        );
    }

    protected function getRules(array $rules, string $operation)
    {
        // if the test does not configured the given operation, we
        // return no rules
        if (!$this->shouldTestOperation($operation)) {
            return [];
        }

        // if the rules contains any of the operations keys, we try to get the rules for
        // that operation, otherwise, we assume the rules must be applied for all operations
        if (array_key_exists('create', $rules) || array_key_exists('update', $rules)) {
            return  $rules[$operation] ?? [];
        }

        return $rules;
    }
}
