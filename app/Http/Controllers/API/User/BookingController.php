<?php

namespace App\Http\Controllers\API\User;

use App\Salon\Client\Booking;
use App\Http\Controllers\Controller;

/**
 * Controller for App\User users manage the salon bookings
 *
 * @see App\User
 * @see App\Salon\Client\Booking
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
        $this->middleware('auth:api-users');
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
