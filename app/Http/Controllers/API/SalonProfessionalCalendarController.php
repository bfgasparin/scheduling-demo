<?php

namespace App\Http\Controllers\API;

use App\Salon;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Queries\Salon\CalendarQuery;

/**
 * Salon Professional`s Calendar actions to used by anyone
 *
 * @see App\Salon
 * @see App\Salon\Employee
 * @see App\Salon\Calendar
 * @see App\Salon\Worker
 */
class SalonProfessionalCalendarController extends Controller
{
    /**
     * Display the calendar of the given professional for the given salon
     *
     * @param int $professional The professional id
     */
    public function index(Salon $salon, int $professional)
    {
        $professional = $salon->employees()->professional()->findOrFail($professional);

        return CalendarQuery::forProfessional($professional)->get(Carbon::today());
    }
}
