<?php

namespace App\Jobs\Salon\Client\Booking;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Salon\{Client\Booking, Client};
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Jobs\Concerns\HandlesSalonClientBookingExceptions;
use App\Notifications\Salon\Client\Booking\CreationFailed as BookingCreationFailed;

/**
 * Create a Booking for a Client of a Salon
 *
 * @see App\Salon\Client
 * @see App\Salon\Service
 * @see App\Salon\Client\Booking
 */
class Create implements ShouldQueue
{
    use Queueable,
        Dispatchable,
        InteractsWithQueue,
        SerializesModels,
        HandlesSalonClientBookingExceptions;

    /** @var int */
    public $tries = 1;

    /** @var App\Salon\Client */
    public $client;

    /** @var array */
    public $data;

    /**
     * Create a new job instance.
     *
     * @param App\Salon\Client $client The client that owns the booking
     * @param array $data
     *
     * @return void
     */
    public function __construct(Client $client, array $data)
    {
        $this->client = $client;
        $this->data = $data;

        $this->onQueue('jobs');
    }

    /**
     * Execute the job.
     *
     * @return App\Salon\Client\Booking
     */
    public function handle() : Booking
    {
        return $this->client->createBooking($this->data);
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
                new BookingCreationFailed($this->client->salon, $this->data, $exception->getMessage())
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
        $this->client->notify($notification);
        $this->client->salon->notify($notification);
    }
}
