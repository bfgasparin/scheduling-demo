<?php

namespace App\Salon;

use App\BelongsToSalon;
use App\Eloquent\Concerns\HasSalon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Eloquent\Concerns\Auth\ScopedByAuthUserSalon;
use App\Salon\Service\Concerns\Auth\ScopedByAuthUserClientVisibility;

/**
 * A service offered by a salon
 *
 * @see Salon
 * @see BelongsToSalon
 */
class Service extends Model implements BelongsToSalon
{
    use SoftDeletes,
        HasSalon,
        ScopedByAuthUserSalon,
        ScopedByAuthUserClientVisibility;

    /**
     * Possible values for client visibility attribute
     */
    const CLIENT_VISIBILITIES = ['always', 'never'];


    protected $table = 'salon_services';

    protected $fillable = [
        'name', 'description', 'price', 'duration', 'cost', 'client_visibility'
    ];

    protected $hidden = ['deleted_at'];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'price' => 'float',
        'duration' => 'integer',
        'cost' => 'float',
        'client_visibility' => 'string',
    ];

    /**
     * Create a new Service model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->client_visibility = 'always';

        parent::__construct($attributes);
    }

    /**
     * The employees that offers the service
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class)->withTimestamps();
    }
}
