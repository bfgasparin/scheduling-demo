<?php

namespace App\Salon\Client;

use Carbon\Carbon;
use App\Salon\{Client, Employee, Service};
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use App\Exceptions\Salon\Client\Booking\AlreadyCanceled;
use App\Salon\Client\Booking\Concerns\InteractsWithSalon;
use App\Salon\Client\Booking\Concerns\HasCalendarAttributes;
use App\Eloquent\Concerns\Auth\{
    ScopedByAuthUserClients,
    ScopedByAuthUserSalonThroughClient
};
use App\Events\Salon\Client\Booking\{
    Creating as BookingCreating,
    Updating as BookingUpdating
};

/**
 * Represents a service booking by a salon client
 * @see App\Salon\Client
 *
 * Client can book services to a salon choosing a professional and a given datetime.
 *
 * On the scheduled datetime, the client appears at the salon, and the
 * professional offers the service into the client.
 *
 * @see App\Salon
 * @see App\Salon\Service
 * @see App\Salon\Employee
 */
class Booking extends Model
{
    use SoftDeletes,
        HasCalendarAttributes,
        InteractsWithSalon,
        ScopedByAuthUserClients,
        ScopedByAuthUserSalonThroughClient;

    protected $table = 'client_bookings';

    protected $fillable = [
        'service_id', 'professional_id', 'date', 'start'
    ];

    protected $hidden = ['deleted_at'];

    protected $dates = ['date', 'deleted_at', 'canceled_at'];

    protected $casts = [
        'date' => 'date',
        'service_price' => 'float',
        'start' => 'string',
    ];

    protected $events = [
        'creating' => BookingCreating::class,
        'updating' => BookingUpdating::class,
    ];

    /**
     * The Client that books the service
     *
     * @see App\Salon\Client
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client() : BelongsTo
    {
        return $this->BelongsTo(Client::class);
    }

    /**
     * The Service booked by the client
     *
     * @see App\Salon\Service
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service() : BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * The professional to offer the Booked Service
     *
     * @see App\Salon\Employee
     * @see self::service
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function professional() : BelongsTo
    {
        return $this->belongsTo(Employee::class)->professional();
    }

    /**
     * Returns it the booking is canceled
     *
     * @return bool
     */
    public function isCanceled() : bool
    {
        return ! is_null($this->canceled_at);
    }

    /**
     * Cancel this booking
     *
     * @return bool
     */
    public function cancel() : bool
    {
        if ($this->isCanceled()) {
            throw new AlreadyCanceled($this);
        }

        $this->canceled_at = Carbon::now();

        return $this->save();
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new BookingCollection($models);
    }
}
