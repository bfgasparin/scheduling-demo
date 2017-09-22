<?php

namespace App\Http\Controllers\API\Salon\Professional;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Salon\Professional\WorkingJorney;
use App\Http\Requests\Salon\Professional\{StoreWorkingJorney, UpdateWorkingJorney};

/**
 * Controller for Professionals to manage its workingJorney
 */
class WorkingJorneyController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Salon\Professional\StoreWorkingJorney  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWorkingJorney $request)
    {
        return response()->json(
            Auth::user()->workingJorney()->create($request->all()),
            201
        );
    }

    /**
     * Display the specified WorkingJorney
     *
     * @param  App\Http\Requests\Salon\Professional\StoreWorkingJorney  $request
     * @return \Illuminate\Http\Response
     */
    public function show(WorkingJorney $workingJorney)
    {
        $this->authorize('view', $workingJorney);

        return $workingJorney;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Salon\Professional\UpdateWorkingJorney  $request
     * @param  string  $workingJorney
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWorkingJorney $request, WorkingJorney $workingJorney)
    {
        return tap($workingJorney)->update($request->all());
    }
}
