<?php

namespace Tests\Feature\API\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Functions to format fixture data for tests
 */
trait FormatsData
{
    /**
     * Returns the input data to use for tests with the resource
     *
     * @return array
     */
    protected function getInputData() : array
    {
        if (method_exists($this, 'inputData')) {
           return $this->inputData();
        }
        return factory($this->model)->make()->toArray();
    }

    protected function makeInputDataUsingAttribute(string $attribute, $value) : array
    {
        $inputData = $this->getInputData();
        $inputData[$attribute] = $value;

        return $inputData;
    }

    /**
     * Returns the existing Resource to for read, update or delete
     * operations CRUD resources
     *
     * @return Model
     */
    protected function getExistingResource() : Model
    {
        if (method_exists($this, 'existingResource')) {
           return $this->existingResource();
        }

        return factory($this->model)->create();
    }

    /**
     * Returns the data to expect to be into database after the insert resource test success
     *
     * @param array $inputData  The request input data
     * @return array
     */
    protected function getDatabaseData(array $inputData) : array
    {
        if (method_exists($this, 'databaseData')) {
           return $this->databaseData($inputData);
        }

        return $inputData;
    }

    /**
     * Returns data to expect into database after the update resource test success
     *
     * @param array $inputData  The request input data
     * @param Model $model      The exising model in database
     * @return array
     */
    protected function getDatabaseDataAfterUpdate(array $inputData, Model $model) : array
    {
        if (method_exists($this, 'databaseDataAfterUpdate')) {
           return $this->databaseDataAfterUpdate($inputData, $model);
        }

        return $inputData;
    }

    /**
     * Returns the data to expect to contains at the response of the request
     *
     * @param array $inputData  The request input data
     * @return array
     */
    protected function getResponseData(array $inputData) : array
    {
        if (method_exists($this, 'responseData')) {
           return $this->responseData($inputData);
        }

        return $inputData;
    }
}
