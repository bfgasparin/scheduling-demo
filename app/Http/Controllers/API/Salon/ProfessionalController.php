<?php

namespace App\Http\Controllers\API\Salon;

use App\Salon\Employee;
use App\Http\Controllers\Controller;
use App\Http\Requests\Salon\StoreEmployee;
use App\Http\Requests\Salon\UpdateEmployee;

/**
 * Controller for App\Salon\Worker users to manage Professioals
 *
 * @see App\Salon\Employee
 * @see App\Salon\Worker
 */
class ProfessionalController extends Controller
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
     * Display a listing of the Professioal Employees.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Employee::professional()->paginate();
    }

   /**
     * Display the specified professional of the given Salon
     *
     * @param App\Salon\Employee $professional
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $professional)
    {
        $this->authorize('view', $professional);

        return $professional;
    }
}

