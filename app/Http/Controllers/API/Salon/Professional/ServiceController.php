<?php

namespace App\Http\Controllers\API\Salon\Professional;

use Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Salon\Professional\StoreServices;

/**
 * Controller for Professionals to manage its Services
 *
 * @see App\Salon\Service
 * @see App\Salon\Employee
 * @see App\Salon\Worker
 */
class ServiceController extends Controller
{
    /**
     * Creates a new instance of the controller
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api-salon-professionals');
    }

    /**
     * List all services offered by the Professional Employee
     *
     * @param Request $request
     */
    public function index()
    {
        return Auth::user()->services;
    }

    /**
     * Update the services of the Professional Employee
     *
     * @param  \Illuminate\Http\StoreServices  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreServices $request)
    {
        return Auth::user()->syncServices(
            collect($request->all())->keyBy('service_id')
        );
    }
}
