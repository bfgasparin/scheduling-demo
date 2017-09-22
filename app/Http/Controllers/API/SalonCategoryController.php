<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Actions for Salon categories to used by anyone
 *
 * @see Salon
 */
class SalonCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get the categories
        with($categories = collect(config('salon.categories')));

        // translate them
        return $categories->transform(function ($item) {
            return __($item);
        });
    }
}
