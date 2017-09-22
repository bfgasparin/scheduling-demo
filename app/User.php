<?php

namespace App;

use App\User\Concerns\Activatable;
use App\Auth\User as Authenticatable;
use App\SmsMessage\ReceivesSmsMessages;
use App\Salon\Client\User as ClientUser;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Eloquent\Concerns\EncryptsAttributes;
use App\Eloquent\Concerns\Salon\Client\User\RepresentsAClient as RepresentsASalonClient;

/**
 * A user on the system
 *
 * A user can login to the system
 * @see App\Auth\User
 *
 * A user can represent Salon Clients in order to booking services on the salons
 * or taken care of salon
 * @see App\Salon\Client
 * @see App\Salon\Client\User
 * @see App\Salon\Service
 * @see App\Salon
 *
 */
class User extends Authenticatable implements ClientUser
{
    use Activatable,
        SoftDeletes,
        Notifiable,
        ReceivesSmsMessages,
        EncryptsAttributes,
        RepresentsASalonClient;

    protected $fillable = ['name', 'email', 'password', 'cellphone'];

    protected $hidden = ['password'];

    protected $dates = ['deleted_at'];

    protected $encrypts = ['password'];

    /**
     * Create a new User model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->active = false;
        parent::__construct($attributes);
    }
}
