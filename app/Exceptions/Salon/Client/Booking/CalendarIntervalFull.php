<?php

namespace App\Exceptions\Salon\Client\Booking;

use Bugsnag;
use App\Exceptions\Logic;
use App\Salon\{Employee, Service};
use App\Queries\Salon\CalendarQuery;

/**
 * Thrown when a a booking was tried to be created in a calendar interval with no space for
 * that booking
 */
class CalendarIntervalFull extends Logic implements Exception
{
    /** @var mixed */
    protected $date;

    /** @var string */
    protected $interval;

    /** @var App\Salon\Employee */
    protected $professional;

    /** @var App\Salon\Service */
    protected $service;

    /**
     * Create a new CalendarIntervalFull exception.
     *
     * @param mixed $date
     * @param string $interval
     * @param Employee $professional
     * @param Service $service

     * @return void
     */
    public function __construct($date, string $interval, Employee $professional, Service $service)
    {
        $this->date = $date;
        $this->interval = $interval;
        $this->professional = $professional;
        $this->service = $service;

        parent::__construct(__(
            'No range was found for booking the service :service  with the professional :professional',
            ['service' => $service->name, 'professional' => $professional->name]
        ));
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
                'data' => [
                    'date' => $this->date,
                    'interval' => $this->interval,
                    'service' => $this->service->toArray(),
                    'professional' => $this->professional->makeHidden(['bookings', 'services'])->toArray(),
                ],
                'professional_services' => $this->professional->services->toArray(),
                'professional_calendar' => CalendarQuery::forProfessional($this->professional)
                    ->get($this->date)->toArray(),
            ]);
        });
    }
}
