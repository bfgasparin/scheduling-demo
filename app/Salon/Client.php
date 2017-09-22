<?php

namespace App\Salon;

use App\User;
use App\Eloquent\Concerns\HasSalon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Salon\Client\Concerns\ManagesBookings;
use App\Eloquent\Concerns\Auth\ScopedByAuthUserSalon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A client of the Salon.
 *
 * A client can book services on the Salon.
 *
 * In order to interact to the system, a client must be represented by a System User
 * @see App\Salon\Client\User;
 * @see self::user
 */
class Client extends Model
{
    use Notifiable,
        HasSalon,
        ManagesBookings,
        ScopedByAuthUserSalon;

    protected $table = 'salon_clients';

    protected $fillable = ['name', 'email', 'cellphone'];

    /**
     * The user representing this client
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
