<?php

namespace Tests\Feature\API\Concerns;

use Illuminate\Foundation\Testing\TestResponse;

/**
 * Functions to help assert response content
 */
trait AssertsResponse
{
    /** @before */
    protected function setUpTestResponse()
    {
        /**
         * Assert that the response has pagination attributes and contain the
         * given superset of the given JSON $data into data attribute
         */
        TestResponse::macro('assertJsonPagination', function (array $data, int $total = null) {
            $this->assertJsonStructure([
                'total', 'per_page', 'current_page', 'from', 'to', 'data',
            ])->assertJsonFragment(['total' => is_null($total) ? count($data) : $total]);

            collect($data)->each(function($item) {
                $this->assertJsonFragment($item);
            });
        });
    }
}
