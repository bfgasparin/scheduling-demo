<?php

namespace Tests\Concerns;

use Event;
use Illuminate\Support\Collection;
use Illuminate\Notifications\Events\NotificationSent;
use Tests\Concerns\CatchesRoutesTouched;

/**
 * Help tests to interacts with Nexmo API. Interactions can be  catching messages sent to Nexmo API
 * or listen for matched route callbacks
 */
trait InteractsWithNexmo
{
    use CatchesRoutesTouched;

    /**
     * Capture and return any mexmo message sent to Nexmo API during the
     * execution of the given callable
     *
     * @param callable $callback
     *
     * @param Illuminate\Support\Collection a collection of Nexmo\Message\Message instance
     */
    protected function nexmoMessagesDuring(callable $callback) : Collection
    {
        $messages = collect([]);

        Event::listen(NotificationSent::class, function ($event) use ($messages) {
            if ($event->channel === 'nexmo') {
                $messages->push($event->response);
            }
        });

        $callback();

        return $messages;
    }


    /**
     * Wait for the Nexmo callback route configured in the system to be touched by the
     * Nexmo server, based on a truth-test callback. Return the request input of
     * the route touched
     *
     * @param  callable|null  $callback
     * @return array  The request input
     */
    public function waitForNexmoCallbackRouteBeTouched($callback = null) : array
    {
        return $this->waitForRouteBeTouched(40, 'api/nexmo/callback', $callback);
    }
}

