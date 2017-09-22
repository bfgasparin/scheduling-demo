<?php

namespace App\Exceptions;

use Illuminate\Http\Request;

/**
 * Helper for exceptions to render an app Response
 */
trait RendersResponse
{
    /**
     * Render the exception
     *
     * @param Illuminate\Http\Request
     *
     * @return mixed The response
     */
    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => $this->getMessage()], 400);
        }
    }
}
