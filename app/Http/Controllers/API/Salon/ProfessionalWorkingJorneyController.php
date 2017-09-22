<?php

namespace App\Http\Controllers\API\Salon;

use App\Salon\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Salon\Professional\WorkingJorney;
use App\Http\Requests\Salon\Professional\{StoreWorkingJorney, UpdateWorkingJorney};

/**
 * Controller for Admin users to manage WorkingJorney of a Professioal
 */
class ProfessionalWorkingJorneyController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Salon\Professional\StoreWorkingJorney  $request
     * @param  App\Salon\Employee $employee
     * @return Illuminate\Http\Response
     */
    public function store(StoreWorkingJorney $request, Employee $professional)
    {
        return response()->json(
            $professional->workingJorney()->create($request->all()),
            201
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Salon\Employee $professional
     * @param  int $workingJorney
     * @return Illuminate\Http\Response
     */
    public function show(Employee $professional, int $workingJorney)
    {
        return $professional->workingJorney()->findOrFail($workingJorney);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Salon\Professional\UpdateWorkingJorney  $request
     * @param  int $workingJorney
     * @return Illuminate\Http\Response
     */
    public function update(UpdateWorkingJorney $request, Employee $professional, int $workingJorney)
    {
        return tap($professional->workingJorney()->findOrFail($workingJorney))
            ->update($request->all());
    }
}
