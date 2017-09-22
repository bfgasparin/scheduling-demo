<?php

namespace App\Jobs\Salon\Client\Booking;

use App\Salon;
use Exception;
use App\Salon\{Client\User as ClientUser, Client};
use App\Jobs\Concerns\HandlesSalonClientBookingExceptions;
use App\Notifications\Salon\Client\Booking\CreationFailed as BookingCreationFailed;

/**
 * Create a booking through a User (Client User). In other words,
 * dispached when a User tries to create a booking
 * @see App\Salon\Client\User
 *
 * If the user trying to book a service is not registered as client on
 * the salon, then he becomes one in order to books the service.
 * @see App\Salon\Client
 *
 * @see App\Salon
 * @see App\Salon\Service
 * @see App\Salon\Client\Booking
 */
class CreateThroughUser
{
    use HandlesSalonClientBookingExceptions;

    /** @var App\Salon\Client\User */
    public $user;

    /** @var App\Salon  */
    public $salon;

    /** @var array */
    public $data;

    /**
     * Create a new job instance.
     *
     * @param App\Salon\Client\User $user The user requesting the booking
     * @param App\Salon $salon The salon to booking
     * @param array $data
     *
     * @return void
     */
    public function __construct(ClientUser $user, Salon $salon, array $data)
    {
        $this->user = $user;
        $this->salon = $salon;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * Return the client represented by the user for which the booking was associated
     *
     * @return App\Salon\Client
     */
    public function handle() : Client
    {
        return tap($this->findClientOrCreate(), function ($client) {
            $this->user->createBookingFor($client, $this->data);
        });
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
                new BookingCreationFailed($this->salon, $this->data, $exception->getMessage())
            );
        }
    }

    /**
     * Find the Salon's client instance the user represents.
     * If not found a client, the User is a new costumer for the salon,
     * so a new Client instance is created for the user
     *
     * @return App\Salon\Client
     */
    protected function findClientOrCreate() : Client
    {
        return $this->user->getClientFor($this->salon)
            ?? $this->user->becomesClientOf($this->salon);
    }

    /**
     * Send the given notification to the booking involved folks (Client User and the Salon)
     *
     * @return void
     */
    protected function notifyInvolvedFolks($notification) : void
    {
        $this->user->notify($notification);
        $this->salon->notify($notification);
    }
}
