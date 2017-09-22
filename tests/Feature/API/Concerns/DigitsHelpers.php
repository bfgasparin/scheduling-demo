<?php

namespace Tests\Feature\API\Concerns;

use Illuminate\Support\Str;

/**
 * Contains helper functions to work with the digits of some value
 */
trait DigitsHelpers
{
    /**
     * Creates a value of the $type with digits greater than $size
     *
     * @param numeric $type
     * @param int $size
     *
     * @return mixed
     */
    protected function makeValueWithDigitsGreaterThan(string $type, int $size)
    {
        $type = Str::studly($type);
        $methodName = "make{$type}valueWithDigitsGreaterThan";
        return $this->$methodName($size);
    }

    /**
     * Creates a value of the $type with digits smaller than $size
     *
     * @param numeric $type
     * @param int $size
     *
     * @return mixed
     */
    protected function makeValueWithDigitsSmallerThan(string $type, int $size)
    {
        $type = Str::studly($type);
        $methodName = "make{$type}ValueWithDigitsSmallerThan";
        return $this->$methodName($size);
    }

    /**
     * Creates a string valuewithdigits of greater than $size
     *
     * @param int $size
     */
    protected function makeStringValueWithDigitsGreaterThan(int $size) : string
    {
        return Str::random($size+1);
    }

    /**
     * Creates a string valuewithdigits of smaller than $size
     *
     * @param int $size
     */
    protected function makeStringValueWithDigitsSmallerThan(int $size) : string
    {
        return Str::random($size-1);
    }

    /**
     * Creates a integer valuewithdigits of greater than $size
     *
     * @param int $size
     */
    protected function makeIntegerValueWithDigitsGreaterThan(int $size) : int
    {
        $value = str_pad(mt_rand(1,9),$size,'0',STR_PAD_RIGHT);

        return (int)($value.mt_rand(1,9));
    }

    /**
     * Creates a integer valuewithdigits of smaller than $size
     *
     * @param int $size
     */
    protected function makeIntegerValueWithDigitsSmallerThan(int $size) : int
    {
        $value = str_pad(mt_rand(1,9),$size,'0',STR_PAD_RIGHT);

        return (int)(substr($value, 0, -1));
    }

    /**
     * Creates a numeric valuewithdigits of greater than $size
     *
     * @param int $size
     */
    protected function makeNumericValueWithDigitsGreaterThan(float $size) : float
    {
        $value = str_pad(mt_rand(1,9),$size,'0',STR_PAD_RIGHT);

        return (float)($value.mt_rand(1,9));
    }

    /**
     * Creates a numeric valuewithdigits of smaller than $size
     *
     * @param int $size
     */
    protected function makeNumericValueWithDigitsSmallerThan(float $size) : float
    {

        $value = str_pad(mt_rand(1,9),$size,'0',STR_PAD_RIGHT);

        return (float)(substr($value, 0, -1));
    }
}

