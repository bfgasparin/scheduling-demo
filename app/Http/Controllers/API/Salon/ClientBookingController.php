<?php

namespace App\Http\Controllers\API\Salon;

use App\Salon\Client;
use App\Http\Controllers\Controller;
use App\Http\Requests\Salon\StoreBooking;
use App\Jobs\Salon\Client\Booking\{
    Cancel as CancelBooking,
    Create as CreateBooking
};

/**
 * Controller for App\Salon\Worker users to manage the bookings of the salon's clients
 *
 * @see App\Salon\Employee
 * @see App\Salon\Client
 * @see App\Salon\Client\Booking
 * @see App\Salon\Worker
 */
class ClientBookingController extends Controller
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
     * Display a listing of Bookings for the Client on the Salon
     *
     * @param App\Salon $salon
     * @return \Illuminate\Http\Response
     */
    public function index(Client $client)
    {
        $this->authorize('view', $client);

        return $client->bookings()->paginate();
    }

    /**
     * Store a newly created Booking in the Salon for the client
     *
     * @param App\Http\Requests\Salon\StoreBooking  $request
     * @param App\Salon\Client $client
     * @return Illuminate\Http\Response
     */
    public function store(StoreBooking $request, Client $client)
    {
        $request->merge([
            'salon_id' => $request->user()->salon_id
        ]);

        dispatch(new CreateBooking($client, $request->all()));

        return response()->json(
            ['message' => __('Your booking request was received and will be processed soon')],
            202
        );
    }

    /**
     * Display the specified Client's Booking on the Salon.
     *
     * @param App\Salon\Client $client
     * @param  int  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client, int $booking)
    {
        return tap($client->bookings()->findOrFail($booking), function ($booking) {
            $this->authorize('view', $booking);
        });
    }

    /**
     * Cancel the specified Client's Booking on the Salon
     *
     * @param App\Salon\Client $client
     * @param  int  $booking
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client, int $booking)
    {
        tap($client->bookings()->findOrFail($booking), function ($booking) {
            $this->authorize('cancel', $booking);
            dispatch(new CancelBooking($booking));
        });

        return response()->json(
            ['message' => __('Your booking cancel request was received and will be processed soon')],
            202
        );
    }
}
