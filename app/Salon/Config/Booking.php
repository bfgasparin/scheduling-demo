<?php

namespace App\Salon\Config;

use Carbon\Carbon;
use App\Eloquent\Concerns\HasSalon;
use Illuminate\Database\Eloquent\Model;
use App\Salon\Config\Booking\Concerns\HasCalendarAttributes;

/**
 * Represents the booking configuration of the salon
 * @see App\Salon
 */
class Booking extends Model
{
    use HasSalon,
        HasCalendarAttributes;

    /**
     * Possible values for calendar_interval field
     * @var array
     */
    const CALENDAR_INTERVAL_VALUES = [15, 20, 25, 30];

    protected $table = 'salon_config_bookings';

    protected $fillable = [
        'cancel_tolerance_for_client_user',
        'create_tolerance_for_client_user',
        'calendar_interval',
    ];

    protected $casts = [
        'cancel_tolerance_for_client_user' => 'integer',
        'create_tolerance_for_client_user' => 'integer',
        'calendar_interval' => 'integer',
    ];

    /**
     * Returns if the Cancel Tolerance time for Client User is exceeded with the given date and time
     *
     * @param mixed  $date
     * @param mixed  $time
     *
     * @return bool
     */
    public function isCancelToleranceForClientUserExceededWith($date, $time) : bool
    {
        return Carbon::parse($date)->setTimeFromTimeString($time)->greaterThan(
            Carbon::now()->subMinutes($this->salon->configBooking->cancel_tolerance_for_client_user)
        );
    }

    /**
     * Returns if the Create Tolerance time for Client User is exceeded with the given date and time
     *
     * @param mixed  $date
     * @param mixed  $time
     *
     * @return bool
     */
    public function isCreateToleranceForClientUserExceededWith($date, $time) : bool
    {
        return Carbon::parse($date)->setTimeFromTimeString($time)->greaterThan(
            Carbon::now()->subMinutes($this->create_tolerance_for_client_user)
        );
    }
}
