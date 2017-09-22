<?php

namespace App\Http\Controllers\API\Salon;

use Carbon\Carbon;
use App\Salon\Employee;
use App\Http\Controllers\Controller;
use App\Queries\Salon\CalendarQuery;

/**
 * Controller for Admin users to see a Calendar of a professional
 *
 * @see App\Salon\Employee
 * @see App\Salon\Calendar
 * @see App\Salon\Worker
 */
class ProfessionalCalendarController extends Controller
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
     * Display the calendar of the given professional
     *
     * @param Employee $professional
     */
    public function index(Employee $professional)
    {
        return CalendarQuery::forProfessional($professional)->get(Carbon::today());
    }
}
