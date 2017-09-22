<?php

namespace App\Http\Controllers\API\Salon;

use App\Salon\Client\Booking;
use App\Http\Controllers\Controller;

/**
 * Controller for App\Salon\Worker users manage the salon bookings
 *
 * @see App\Salon\Employee
 * @see App\Salon\Client\Booking
 * @see App\Salon\Worker
 */
class BookingController extends Controller
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
     * Display a listing of Bookings on the Salon
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Booking::paginate();
    }
}
