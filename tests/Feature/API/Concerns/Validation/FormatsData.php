<?php

namespace Tests\Feature\API\Concerns\Validation;

use Illuminate\Database\Eloquent\Model;

/**
 * Functions to format fixture data for validation tests
 */
trait FormatsData
{
    /**
     * Returns the data to expect to be into database after failure on validation rule
     * create test

     * @param array $inputData  The request input data
     * @return array
     */
    protected function getDatabaseMissingDataAfterCreateFailure(array $inputData) : array
    {
        if (method_exists($this, 'databaseMissingDataAfterCreateFailure')) {
           return $this->databaseMissingDataAfterCreateFailure($inputData);
        }

        return $inputData;
    }

    /**
     * Returns the data to expect to be missing into database after failure on validation rule
     * update test
     *
     * @param array $inputData  The request input data
     * @return array
     */
    protected function getDatabaseMissingDataAfterUpdateFailure(array $inputData) : array
    {
        if (method_exists($this, 'databaseMissingDataAfterUpdateFailure')) {
           return $this->databaseMissingDataAfterUpdateFailure($inputData);
        }

        return $inputData;
    }

    /**
     * Returns the data to expect to be into database after error on validation rule
     * update test
     *
     * @param Model $model The existing model
     * @return array
     */
    protected function getDatabaseDataAfterUpdateFailure(Model $model) : array
    {
        if (method_exists($this, 'databaseDataAfterUpdateFailure')) {
           return $this->databaseDataAfterUpdateFailure($model);
        }

        return $model->toArray();
    }
}
