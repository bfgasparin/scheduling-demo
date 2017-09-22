<?php

namespace App\Http\Controllers\API\User;

use Auth;
use App\Salon;
use App\Http\Controllers\Controller;
use App\Http\Requests\Salon\StoreBooking;
use App\Jobs\Salon\Client\Booking\{
    CreateThroughUser as CreateBookingThroughUser,
    CancelThroughUser as CancelBookingThroughUser
};

/**
 * Actions for Salon's Bookings to be used by User
 *
 * @see App\Salon\Client\Booking
 * @see App\User
 */
class SalonBookingController extends Controller
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
     * Display a listing of Bookings for the User on the Salon
     *
     * @param App\Salon $salon
     * @return \Illuminate\Http\Response
     */
    public function index(Salon $salon)
    {
        $this->authorize('view', Auth::user());

        return $salon->bookings()->paginate();
    }

    /**
     * Store a newly created Booking in the Salon for the User
     *
     * @param App\Http\Requests\Salon\StoreBooking  $request
     * @param App\Salon $salon
     * @return Illuminate\Http\Response
     */
    public function store(StoreBooking $request, Salon $salon)
    {
        dispatch(new CreateBookingThroughUser($request->user(), $salon, $request->all()));

        return response()->json(
            ['message' => __('Your booking request was received and will be processed soon')],
            202
        );
    }

    /**
     * Display the specified User's Booking on the Salon.
     *
     * @param Salon $salon
     * @param  int  $booking
     * @return Illuminate\Http\Response
     */
    public function show(Salon $salon, int $booking)
    {
        return tap($salon->bookings()->findOrFail($booking), function ($booking) {
            $this->authorize('view', $booking);
        });
    }

    /**
     * Cancel the specified User's Booking on the salon
     *
     * @param Salon $salon
     * @param  int  $booking
     * @return \Illuminate\Http\Response
     */
    public function destroy(Salon $salon, int $booking)
    {
        tap($salon->bookings()->findOrFail($booking), function ($booking) {
            $this->authorize('cancel', $booking);
            dispatch(new CancelBookingThroughUser(Auth::user(), $booking));
        });

        return response()->json(
            ['message' => __('Your booking cancel request was received and will be processed soon')],
            202
        );
    }
}
