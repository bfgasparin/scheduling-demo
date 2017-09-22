<?php

namespace Tests\Concerns;

use Exception;

/**
 * Help tests to catch and assert excetions thrown during the test
 */
trait CatchesException
{
    /**
     * Catches the given exception thrown in the callable executor and return it
     *
     * @param mixed $class
     * @param mixed $executor
     */
    protected function catchException(string $class, callable $executor)
    {
        try {
            $executor();
        } catch (Exception $e) {
            if(is_a($e, $class)) {
                return $e;
            }

            throw $e;
        }

        throw new Exception("No exception thrown. Expected exception {$class}");
    }
}

