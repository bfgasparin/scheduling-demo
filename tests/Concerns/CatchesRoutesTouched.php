<?php

namespace Tests\Concerns;

use Cache;
use PHPUnit\Framework\Assert as PHPUnit;

/**
 * Help tests to catch and asserts routes touched by an external service during the test
 */
trait CatchesRoutesTouched
{
    /**
     * Wait a given amount of time for the given uri to be touched by an external services
     * based on the a truth-test callback. Return the request input from the route
     *
     * @param int $seconds
     * @param string $uri
     * @param $callback|null
     *
     * @return array  The request input sent to the route tuuched
     */
    public function waitForRouteBeTouched(int $seconds, string $uri, $callback = null) : array
    {
        // Check if the uri was touched using cache touched routes from MarkRouteAsTouched Listener
        // @see Tests\Support\Listeners\MarkRouteAsTouched
        return retry($seconds, function () use ($uri, $callback) {
            // If the uri was marked to the cache by MarkRouteAsTouched Listener, we can assume
            // the uri was touched, otherwise we keep waiting to the uri on the cache to appears
            PHPUnit::assertTrue(Cache::has($uri), "The expected [$uri] uri was not touched.");

            // If uri was found, we check if the request input of the its request passes
            // into the given truth-test callback. If no callback is set, we considered the
            // uri was matched.
            if (is_null($callback)) {
                return Cache::pull($uri);
            }else if (is_callable($callback)) {
                return tap(Cache::pull($uri), function ($input) use ($uri, $callback) {
                    PHPUnit::assertTrue($callback($input), "The expected [$uri] uri was not touched.");
                });
            }
        }, 1000);
    }
}

