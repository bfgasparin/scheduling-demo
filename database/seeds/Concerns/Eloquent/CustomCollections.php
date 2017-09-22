<?php

use Faker\Generator as FakerGenerator;

use Illuminate\Database\Eloquent\Collection;

/**
 * Decoretes Eloquent Collection with custom methods
 * to help seeding database.
 */
trait CustomCollections
{
    /**
     * Adds some macros on eloquent collections to help
     * database seeding
     *
     * @see Illuminate\Database\Eloquent\Relations\Relation
     * Illuminate\Support\Traits\Macroable
     */
    public function decorateEloquentCollection() : void
    {
        Collection::macro('toProfessionalServiceAttributes', function () {
            return $this->map(function ($service) {
                return collect(['service_id' => $service->id, 'duration' => $service->duration]);
            })->values();
        });
    }
}
