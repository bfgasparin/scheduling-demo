<?php

namespace App\Exceptions\Salon\Client\Booking;

use Bugsnag;
use App\Exceptions\Logic;
use App\Salon\{Employee, Service};

/**
 * Thrown when a service is not offered by a professional
 */
class ServiceNotOfferedByProfessional extends Logic implements Exception
{

    /** @var App\Salon\Employee */
    protected $professional;

    /** @var App\Salon\Service */
    protected $service;

    /**
     * Create a new ServiceNotOfferedByProfessional exception.
     *
     * @param Employee $professional
     * @param Service $service

     * @return void
     */
    public function __construct(Employee $professional, Service $service)
    {
        $this->professional = $professional;
        $this->service = $service;

        parent::__construct(__('The professional :professional does not offer the given :service',
            ['professional' => $professional->name, 'service' => $service->name]
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
            $report->setSeverity('error');
            $report->setMetaData([
                'professional' => $this->professional->toArray(),
                'service' => $this->service->toArray(),
            ]);
        });
    }
}
