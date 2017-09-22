<?php

namespace App\Salon\Employee\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Salon\{Service, Professional\Service as ProfessionalService};

/**
 * Contains methods to help the professional to manage the services it offers
 */
trait ManageServices
{
    /**
     * The services the professional offers
     */
    public function services() : BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'professional_service', 'professional_id')
            ->withPivot('duration')
            ->using(ProfessionalService::class)
            ->withTimestamps();
    }

    /**
     * Return if the professional offers the given service
     *
     * @param App\Salon\Service $service
     *
     * @return bool
     */
    protected function offersService(Service $service) : bool
    {
        return $this->services->contains($service);
    }

    /**
     * Override the list of services the professional can offer
     *
     * The list of services must be passed in the format of
     *
     *     [
     *         4 => ['duration' => 4]
     *         12 => ['duration' => 4]
     *     ]
     *
     *  where the key is the service, and the value is the vlaues of Professional Service
     *
     *  Or just pass an array of service ids
     *
     *     [4, 12]
     *
     * @see App\Salon\Professional\Service
     *
     * @param mixed $services  The new list of services
     *
     * @return array
     */
    public function syncServices($services) : array
    {
        return $this->services()->sync($services);
    }

    /**
     * Add the given service to the list of services the professional can offer
     *
     * To attach more than one service at a time, you can pass a the list of services
     *
     *     [
     *         4 => ['duration' => 4]
     *         12 => ['duration' => 4]
     *     ]
     *
     *  where the key is the service, and the value is the vlaues of Professional Service
     *
     *  Or just pass an array of service ids
     *
     *     [4, 12]
     *
     *
     * @see App\Salon\Professional\Service
     *
     * @param mixed $services The new list of services
     *
     * @return void
     */
    public function attachService($serviceId, array $attributes = [], $touch = true)
    {
        $this->services()->attach($serviceId, $attributes, $touch);
    }
}
