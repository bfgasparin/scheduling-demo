<?php

namespace App\Http\Controllers\API\Salon\Professional;

use Auth;
use Carbon\Carbon;
use App\Queries\Salon\CalendarQuery;
use App\Http\Controllers\Controller;

/**
 * Controller for Professionals to see its calendar
 *
 * @see App\Salon\Calendar
 * @see App\Salon\Employee
 * @see App\Salon\Worker
 */
class CalendarController extends Controller
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
     * Display the Professional calendar
     */
    public function index()
    {
        return CalendarQuery::forProfessional(Auth::user())->get(Carbon::today());
    }
}
