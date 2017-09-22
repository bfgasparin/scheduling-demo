<?php

namespace Tests\Feature\API\Concerns;

use Illuminate\Support\Str;

/**
 * Contains helper functions to work with size of some value
 */
trait SizeHelpers
{
    /**
     * Creates a value of the $type greater than $size
     *
     * @param numeric $type
     * @param mixed $size
     *
     * @return mixed
     */
    protected function makeValueGreaterThan(string $type, $size)
    {
        $type = Str::studly($type);
        $methodName = "make{$type}valueGreaterThan";
        return $this->$methodName($size);
    }

    /**
     * Creates a value of the $type smaller than $size
     *
     * @param numeric $type
     * @param int $size
     *
     * @return mixed
     */
    protected function makeValueSmallerThan(string $type, int $size)
    {
        $type = Str::studly($type);
        $methodName = "make{$type}ValueSmallerThan";
        return $this->$methodName($size);
    }

    /**
     * Creates a string value of greater than $size
     *
     * @param int $size
     */
    protected function makeStringValueGreaterThan(int $size) : string
    {
        return Str::random($size+1);
    }

    /**
     * Creates a string value of smaller than $size
     *
     * @param int $size
     */
    protected function makeStringValueSmallerThan(int $size) : string
    {
        return Str::random($size-1);
    }

    /**
     * Creates a integer value of greater than $size
     *
     * @param int $size
     */
    protected function makeIntegerValueGreaterThan(int $size) : int
    {
        return $size+1;
    }

    /**
     * Creates a integer value of smaller than $size
     *
     * @param int $size
     */
    protected function makeIntegerValueSmallerThan(int $size) : int
    {
        return $size-1;
    }

    /**
     * Creates a numeric value of greater than $size
     *
     * @param int $size
     */
    protected function makeNumericValueGreaterThan(float $size) : float
    {
        return $size+0.1;
    }

    /**
     * Creates a numeric value of smaller than $size
     *
     * @param int $size
     */
    protected function makeNumericValueSmallerThan(float $size) : float
    {
        return $size-0.1;
    }
}

