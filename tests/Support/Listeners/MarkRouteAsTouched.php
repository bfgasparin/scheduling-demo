<?php

namespace Tests\Support\Listeners;

use Cache;
use Illuminate\Routing\Events\RouteMatched;

/**
 * Listen for matched routes in the system and mark them as touched using the Cache driver.
 *
 * Can be helpfull to assert if some uri was touched by an external server during an
 * integration test (like asserting if an extenal API touches some callback uri
 * after been requested)
 */
class MarkRouteAsTouched
{
    /**
     * Handle the event.
     *
     * @param  Illuminate\Routing\Events\RouteMatched $event
     * @return void
     */
    public function handle(RouteMatched $event)
    {
        Cache::forever($event->route->uri, $event->request->toArray());
    }
}
