<?php

namespace App\Eloquent\Concerns\Salon\Client\User;

use App\Salon\Client;
use App\Salon\Client\Booking;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Exceptions\Salon\Client\Booking\CreateToleranceForClientUserExceeded;
use App\Jobs\Salon\Client\Booking\{Cancel as BookingCancel, Create as BookingCreate};
use App\Exceptions\Salon\Client\Booking\{
    BookingDoesNotBelongsToClientUser,
    CancelToleranceForClientUserExceeded
};

/**
 * Contains methods to help the client user to manage its bookings
 */
trait ManageClientBookings
{
    /**
     * The bookings of the client user
     *
     * @see App\Salon\Client\Booking
     *
     * @return Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function bookings() : HasManyThrough
    {
        return $this->hasManyThrough(Booking::class, Client::class);
    }

    /**
     * Create a new booking for the given Client
     *
     *
     * @param Client $client
     * @param array $data
     *
     * @return self
     */
    public function createBookingFor(Client $client, array $data) : self
    {
        [$date, $time] = collect($data)->only(['date', 'start'])->values();

        if ($client->salon->configBooking->isCreateToleranceForClientUserExceededWith($date, $time)) {
            throw new CreateToleranceForClientUserExceeded($client->salon->configBooking, $this, $data);
        }

        dispatch(new BookingCreate($client, $data));

        return $this;
    }

    /**
     * Cancel a Booking
     *
     * @param Booking $booking
     *
     * @return seld
     */
    public function cancelBooking(Booking $booking) : self
    {
        if ($booking->exceedsCancelToleranceForClientUser()) {
            throw new CancelToleranceForClientUserExceeded($booking);
        }

        if (! $this->isOwnerOfBooking($booking)) {
            throw new BookingDoesNotBelongsToClientUser($this, $booking);
        }

        dispatch(new BookingCancel($booking));

        return $this;
    }

    /**
     * Return if the user is owner of the given booking
     *
     * @param Booking $booking
     *
     * @return bool
     */
    public function isOwnerOfBooking(Booking $booking) : bool
    {
        return $this->bookings->contains($booking);
    }
}
