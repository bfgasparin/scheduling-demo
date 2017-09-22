<?php

namespace Tests\Feature\API\Concerns;

use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

/**
 * Contains helper functions for CRUD Resource tests
 */
trait CRUDResourceTestable
{
    /**
     * Returns the name of the resource table into the database to help to test the CRUDs
     *
     * @return string
     */
    protected function getCRUDTable()
    {
        if (!isset($this->table)) {
            throw new RuntimeException(
                "You must declare the table attribute in your test class indicanting the table representing the model      " . PHP_EOL .
                "to test the CRUD. Example:                                                                                " . PHP_EOL . PHP_EOL .
                '  /**                                                                                                     ' . PHP_EOL .
                '  * The table associated with the resource used by the Simple CRUD test                                   ' . PHP_EOL .
                '  *                                                                                                       ' . PHP_EOL .
                '  * @see SimpleCRUDResourceTests                                                                          ' . PHP_EOL .
                '  */                                                                                                      ' . PHP_EOL .
                "  protected \$table = 'users';                                                                            "
            );
        }

        return $this->table;
    }

    /**
     * Returns the name of the resource to test the CRUDs
     *
     * @return string
     */
    protected function getCRUDResource() : string
    {
        if (!isset($this->table)) {
            throw new RuntimeException(
                "You must declare the resource attribute in your test class indicanting the resource to test the CRUD      " . PHP_EOL .
                "Example:                                                                                                  " . PHP_EOL . PHP_EOL .
                '  /**                                                                                                     ' . PHP_EOL .
                '  * The resource name on uri to be used by the Simple CRUD tests                                          ' . PHP_EOL .
                '  *                                                                                                       ' . PHP_EOL .
                '  * @see SimpleCRUDResourceTests                                                                          ' . PHP_EOL .
                '  */                                                                                                      ' . PHP_EOL .
                "  protected \$resource = 'users';                                                                            "
            );
        }

        return $this->resource;
    }

    /**
     * Returns if the resource to test the CRUD has solfDelete
     *
     * @return bool
     */
    protected function resourceHasSoftDelete() : bool
    {
        return $this->softDeletes ?? true;
    }

    /**
     * Returns the data to expect to be missing into database after delete the resource
     *
     * @param Model  $model The resource deleted
     * @return array
     */
    protected function getDatabaseMissingDataAfterDelete(Model $model) : array
    {
        return $model->toArray();
    }

    /**
     * Returns the data to expect to be into database after error on deleting the resource
     *
     * @param Model  $model The resource was tried to delete
     * @return array
     */
    protected function getDatabaseDataAfterDeleteFailure(Model $model) : array
    {
        return $model->toArray();
    }

    /**
     * Returns if the given CRUD operation should be tested
     *
     * @return bool
     */
    protected function shouldTestOperation(string $operationName) : bool
    {
        if (!isset($this->operations)) {
            throw new RuntimeException(
                "You must declare the operations attribute in your test class indicanting the operations to test. Example: " . PHP_EOL . PHP_EOL .
                '  /**                                                                                                     ' . PHP_EOL .
                '  * CRUD operations to test for the resource                                                              ' . PHP_EOL .
                '  *                                                                                                       ' . PHP_EOL .
                '  * @see SimpleCRUDResourceTests                                                                          ' . PHP_EOL .
                '  */                                                                                                      ' . PHP_EOL .
                "  protected \$operations = ['create', 'update', 'delete'];                                                "
            );
        }
        return in_array($operationName, $this->operations);
    }

    /**
     * Setup the application before run the given $operation CRUD test
     *
     * This method runs after generating CRUD input data, but before call the resources endpoints.
     *
     * So you create fixtures here ahd use them as the input data of your tests. You can
     * use the before phpunit annotation intead
     * @see self::inputData
     * @see https://phpunit.de/manual/current/en/phpunit-book.html#appendixes.annotations.before
     *
     * @param string $operation
     * @return self
     */
    public function setUpApplicationForTestOn(string $operation = null) : self
    {
        $operation = Str::studly($operation);

        $setUpMethod = "setUpAppBefore{$operation}Resource";
        if(method_exists($this, $setUpMethod)) {
            $this->$setUpMethod();
        }

        return $this;
    }
}
