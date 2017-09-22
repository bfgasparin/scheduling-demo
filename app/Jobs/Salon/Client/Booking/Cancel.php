<?php

namespace App\Jobs\Salon\Client\Booking;

use Exception;
use Illuminate\Bus\Queueable;
use App\Salon\{Client\Booking, Client};
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Jobs\Concerns\HandlesSalonClientBookingExceptions;
use App\Notifications\Salon\Client\Booking\{
    Canceled as BookingCanceled,
    CancelFailed as BookingCancelFailed
};

/**
 * Cancels a Booking of a client
 *
 * Cancels a Booking and notities the client and the salon if success or fail
 */
class Cancel implements ShouldQueue
{
    use Queueable,
        Dispatchable,
        InteractsWithQueue,
        SerializesModels,
        HandlesSalonClientBookingExceptions;

    /** @var int */
    public $tries = 1;

    /** @var Booking */
    public $booking;

    /**
     * Create a new job instance.
     *
     * @param Booking $booking
     *
     * @return void
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;

        $this->onQueue('jobs');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->booking->cancel();

        $this->notifyInvolvedFolks(new BookingCanceled($this->booking));
    }

    /**
     * Notify the client and the salon when the job failed to proccess
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception) : void
    {
        if ($this->isBookingDomainLogicException($exception)) {
            $this->notifyInvolvedFolks(
                new BookingCancelFailed($this->booking, $exception->getMessage())
            );
        }
    }

    /**
     * Send the given notification to the booking involved folks (Client and the Salon)
     *
     * @return void
     */
    protected function notifyInvolvedFolks($notification) : void
    {
        $this->booking->client->notify($notification);
        $this->booking->salon->notify($notification);
    }
}
