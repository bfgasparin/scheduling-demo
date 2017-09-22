<?php

namespace App\Http\Controllers\API\Salon\Professional;

use App\Http\Controllers\Controller;
use App\Salon\Professional\{WorkingJorney, WorkingJorney\Schedule};
use App\Http\Requests\Salon\Professional\WorkingJorney\{StoreSchedule, UpdateSchedule};

/**
 * Controller for Professionals to manage its WorkingJorney's Schedules
 */
class WorkingJorneyScheduleController extends Controller
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

        return $workingJorney->schedules()->paginate();
    }

    /**
     * Store a newly Schedule to the WorkingJorney in storage.
     *
     * @param App\Http\Requests\Salon\Professional\WorkingJorney\StoreSchedule $request
     * @param App\Salon\Professional\WorkingJorney $workingJorney
     */
    public function store(StoreSchedule $request, WorkingJorney $workingJorney)
    {
        return response()->json(
            $workingJorney->schedules()->create($request->all()),
            201
        );
    }

    /**
     * Display the specified Schedule.
     *
     * @param App\Salon\Professional\WorkingJorney $workingJorney
     * @param int $schedule
     */
    public function show(WorkingJorney $workingJorney, int $schedule)
    {
        $this->authorize('view', $workingJorney);

        return $workingJorney->schedules()->findOrFail($schedule);
    }

    /**
     * Update the specified Schedule in storage.
     *
     * @param App\Http\Requests\Salon\Professional\WorkingJorney\UpdateSchedule $request
     * @param App\Salon\Professional\WorkingJorney $workingJorney
     * @param int $schedule
     */
    public function update(UpdateSchedule $request, WorkingJorney $workingJorney, int $schedule)
    {
        return tap($workingJorney->schedules()->findOrFail($schedule))
            ->update($request->all());
    }

    /**
     * Remove the specified Schedule from storage.
     *
     * @param App\Salon\Professional\WorkingJorney $workingJorney
     * @param int $schedule
     */
    public function destroy(WorkingJorney $workingJorney, int $schedule)
    {
        $this->authorize('delete', $workingJorney);

        $workingJorney->schedules()->findOrFail($schedule)->delete();

        return response()->json(['message' => __('Schedule removed successfully')]);
    }
}
