<?php

namespace App\Salon;

use App\Salon;
use App\Salon\Worker as SalonWorker;
use App\Auth\User as Authenticatable;
use App\Eloquent\Concerns\WorksOnSalon;
use App\Eloquent\Concerns\EncryptsAttributes;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Eloquent\Concerns\Auth\ScopedByAuthUserSalon;
use App\Salon\Employee\Concerns\HasProfessionalResponsabilities;

/**
 * An employee of a Salon
 * Employees offers service for users from a salon
 *
 * @see App\User
 * @see App\Salon
 * @see App\Salon\Worker
 * @see App\Salon\Service
 */
class Employee extends Authenticatable implements SalonWorker
{
    use SoftDeletes,
        EncryptsAttributes,
        WorksOnSalon,
        ScopedByAuthUserSalon,
        HasProfessionalResponsabilities;

    protected $table = 'salon_employees';

    protected $fillable = ['name', 'email', 'password'];

    protected $encrypts = ['password'];

    protected $hidden = ['deleted_at', 'password'];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'is_admin' => 'boolean',
        'is_professional' => 'boolean',
    ];

    public function __construct(array $attributes = [])
    {
        $this->is_admin = false;
        $this->is_professional = true;

        parent::__construct($attributes);
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new EmployeeCollection($models);
    }
}
