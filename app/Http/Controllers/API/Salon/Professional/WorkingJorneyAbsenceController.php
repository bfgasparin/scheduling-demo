<?php

namespace App\Http\Controllers\API\Salon\Professional;

use App\Http\Controllers\Controller;
use App\Salon\Professional\{WorkingJorney, WorkingJorney\Absence};
use App\Http\Requests\Salon\Professional\WorkingJorney\{StoreAbsence, UpdateAbsence};

/**
 * Controller for Professionals to manage its WorkingJorney's Absences
 */
class WorkingJorneyAbsenceController extends Controller
{
    /**
     * Creates a new instance of the controller
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api-salon-admins,api-salon-professionals');
    }

    /**
     * Display a listing of the resource.
     *
     * @param App\Salon\Professional\WorkingJorney $workingJorney
     * @return \Illuminate\Http\Response
     */
    public function index(WorkingJorney $workingJorney)
    {
        $this->authorize('view', $workingJorney);

        return $workingJorney->absences()->paginate();
    }

    /**
     * Store a newly Absence to the WorkingJorney in storage.
     *
     * @param App\Http\Requests\Salon\Professional\WorkingJorney\StoreAbsence $request
     * @param App\Salon\Professional\WorkingJorney $workingJorney
     */
    public function store(StoreAbsence $request, WorkingJorney $workingJorney)
    {
        return response()->json(
            $workingJorney->absences()->create($request->all()),
            201
        );
    }

    /**
     * Display the specified Absence.
     *
     * @param App\Salon\Professional\WorkingJorney $workingJorney
     * @param int $absence
     */
    public function show(WorkingJorney $workingJorney, int $absence)
    {
        $this->authorize('view', $workingJorney);

        return $workingJorney->absences()->findOrFail($absence);
    }

    /**
     * Update the specified Absence in storage.
     *
     * @param App\Http\Requests\Salon\Professional\WorkingJorney\UpdateAbsence $request
     * @param App\Salon\Professional\WorkingJorney $workingJorney
     * @param int $absence
     */
    public function update(UpdateAbsence $request, WorkingJorney $workingJorney, int $absence)
    {
        return tap($workingJorney->absences()->findOrFail($absence))
            ->update($request->all());
    }

    /**
     * Remove the specified Absence from storage.
     *
     * @param App\Salon\Professional\WorkingJorney $workingJorney
     * @param int $absence
     */
    public function destroy(WorkingJorney $workingJorney, int $absence)
    {
        $this->authorize('delete', $workingJorney);

        $workingJorney->absences()->findOrFail($absence)->delete();

        return response()->json(['message' => __('Absence removed successfully')]);
    }
}
