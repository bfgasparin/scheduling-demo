<?php

namespace App\Exceptions\Salon\Client\Booking;

use Bugsnag;
use Carbon\Carbon;
use App\Exceptions\InvalidArgument;
use App\Salon\Client\User as ClientUser;
use App\Salon\Config\Booking as ConfigBooking;

/**
 * Thrown when a client user tries to cancel a booking that exceeds the Create Tolerance time for
 * Client User of the Salon
 */
class CreateToleranceForClientUserExceeded extends InvalidArgument implements Exception
{
    /** @var App\Salon\Config\Booking */
    protected $config;

    /** @var App\Salon\Client\User */
    protected $user;

    /** @var array The booking data */
    protected $data;

    /**
     * Create a new CreateToleranceForClientUserExceeded exception.
     *
     * @param  ConfigBooking  $configBooking
     * @param  App\Salon\Client\User  $user
     * @param  array  $data The booking data
     * @return void
     */
    public function __construct(ConfigBooking $config, ClientUser $user, array $data)
    {
        $this->config = $config;
        $this->user = $user;
        $this->data = $data;

        parent::__construct(
            __(
                "The booking should be created with at least :createToleranceForClientUser minutes before current time.",
                [
                    'createToleranceForClientUser' => $config->create_tolerance_for_client_user,
                ]
            )
        );
    }

    /**
     * Report the exception
     *
     * @return void
     */
    public function report() : void
    {
        Bugsnag::notifyException($this, function ($report) {
            $report->setSeverity('info');
            $report->setMetaData([
                'user' => $this->user->toArray(),
                'current_time' => Carbon::now(),
                'data' => json_encode($this->data),
                'config_booking' => $this->config->toArray(),
            ]);
        });
    }
}
