<?php

namespace App\Http\Controllers\API\Salon;

use App\Salon\Employee;
use App\Http\Controllers\Controller;
use App\Http\Requests\Salon\StoreEmployee;
use App\Http\Requests\Salon\UpdateEmployee;

/**
 * Controller for App\Salon\Worker users to manage Employees
 *
 * @see App\Salon\Employee
 * @see App\Salon\Worker
 */
class EmployeeController extends Controller
{
    /**
     * Creates a new instance of the controller
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api-salon-admins,api-salon-professionals')->only('update', 'show');
        $this->middleware('auth:api-salon-admins')->except('create', 'edit', 'update', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Employee::paginate();
    }

    /**
     *
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\StoreEmployee  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEmployee $request)
    {
        with($salon = $request->user()->getSalon());

        return response(
            $salon->employees()->create($request->all()),
            201
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        $this->authorize('view', $employee);

        return $employee;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\UpdateEmployee  $request
     * @param  Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEmployee $request, Employee $employee)
    {
        return tap($employee)->update($request->except('password'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return response()->json(['message' => __('Employee removed successfully')]);
    }
}
