<?php

namespace App\Http\Controllers\API\Salon;

use App\Salon\Employee;
use App\Http\Controllers\Controller;
use App\Http\Requests\Salon\Professional\StoreServices;

/**
 * Controller for Admin users to manage services of a Professioal
 *
 * @see App\Salon\Service
 * @see App\Salon\Employee
 * @see App\Salon\Worker
 */
class ProfessionalServiceController extends Controller
{
    /**
     * Creates a new instance of the controller
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api-salon-admins');
    }

    /**
     * List all services offered by the Professional Employee
     *
     * @param Request $request
     */
    public function index(Employee $professional)
    {
        return $professional->services;
    }

    /**
     * Update the services of the Professional Employee
     *
     * @param  \Illuminate\Http\StoreServices  $request
     * @param  \App\Salon\Employee  $professional
     * @return \Illuminate\Http\Response
     */
    public function store(StoreServices $request, Employee $professional)
    {
        return $professional->syncServices(
            collect($request->all())->keyBy('service_id')
        );
    }
}
