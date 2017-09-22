<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Salon\Config\Booking as ConfigBooking;
use App\Salon\{Client\Booking, Employee, Client, Service, Professional\WorkingJorney};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasOne, HasMany, HasManyThrough};

/**
 * A salon
 * A Salon offers Services to Users
 * A Salon has Employees that offer the Services to the Users
 *
 * @see App\Auth\User
 * @see App\Salon\Service
 */
class Salon extends Model
{
    use Notifiable,
        SoftDeletes;

    protected $fillable = ['name', 'email', 'description', 'category'];

    protected $hidden = ['deleted_at'];

    protected $dates = ['deleted_at'];

    /**
     * The employees that works on the salon
     * @see App\Salon\Employee
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees() : HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * The services offered by the salon
     *
     * The services are offers by the salon`s employees
     * @see self::employees
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function services() : HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * The clients of the salon
     *
     * @see App\Salon\Client
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clients() : HasMany
    {
        return $this->hasMany(Client::class);
    }

    /**
     * The bookings of the salon
     *
     * @see App\Salon\Client\Booking
     *
     * @return Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function bookings() : HasManyThrough
    {
        return $this->hasManyThrough(Booking::class, Client::class);
    }

    /**
     * The booking configuration of the salon
     *
     * @see App\Salon\Config\Booking
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function configBooking() : HasOne
    {
        return $this->hasOne(ConfigBooking::class);
    }

    /**
     * The workingJorneys of the professionals
     *
     * @see App\Salon\Employee
     *
     * @return Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function workingJorneys() : HasManyThrough
    {
        return $this->HasManyThrough(WorkingJorney::class, Employee::class, 'salon_id', 'professional_id');
    }
}
