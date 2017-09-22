<?php

namespace App\Jobs\Salon\Client\Booking;

use Exception;
use App\Salon;
use App\Jobs\Concerns\HandlesSalonClientBookingExceptions;
use App\Jobs\Salon\Client\Booking\Cancel as BookingCancel;
use App\Salon\{Client\Booking, Client\User as ClientUser};
use App\Notifications\Salon\Client\Booking\CancelFailed as BookingCancelFailed;

/**
 * Cancel a booking through a User (Client User). In other words,
 * dispached when a User tries to cancel a booking
 *
 * @see App\Salon\Client\User
 *
 * @see App\Salon\Client\Booking
 * @see App\Salon
 */
class CancelThroughUser
{
    use HandlesSalonClientBookingExceptions;

    /** @var App\Salon\Client\User */
    public $user;

    /** @var App\Salon\Client\Booking  */
    public $booking;

    /**
     * Create a new job instance.
     *
     * @param App\Salon\Client\User $user The user requesting the booking
     * @param App\Salon\Client\Booking $booking The booking to cancel
     *
     * @return void
     */
    public function __construct(ClientUser $user, Booking $booking)
    {
        $this->user = $user;
        $this->booking = $booking;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() : void
    {
        $this->user->cancelBooking($this->booking);
    }

    /**
     * Notify the client user and the salon when the job failed to proccess
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception) : void
    {
        if ($this->isBookingDomainLogicException($exception)) {
            $this->notifyInvolvedFolks(
                new BookingCancelFailed($this->booking, $exception->getMessage()));
        }
    }

    /**
     * Send the given notification to the booking involved folks (Client and the Salon)
     *
     * @return void
     */
    protected function notifyInvolvedFolks($notification) : void
    {
        $this->user->notify($notification);
        $this->booking->salon->notify($notification);
    }
}
