<?php

namespace App\Salon\Calendar;

use ArrayAccess;
use Carbon\Carbon;
use JsonSerializable;
use App\Salon\Employee;
use InvalidArgumentException;
use App\Salon\Client\BookingCollection;
use App\Eloquent\Concerns\HasDateFilters;
use Illuminate\Contracts\Support\{Arrayable, Jsonable};
use App\Exceptions\Salon\Client\Booking\CalendarIntervalFull;

/**
 * Represents a Calendar Item
 * @see App\Calendar
 */
class Item implements ArrayAccess, Arrayable, Jsonable, JsonSerializable
{
    use HasDateFilters;

    /** @var string */
    public $id;

    /** @var Carbon\Carbon */
    public $date;

    /** @var string */
    public $interval;

    /** @var App\Salon\Employee */
    public $professional;

    /** @var App\Salon\Client\Booking */
    public $bookings;

    /** @var bool */
    public $blocked;

    /** @var string */
    public $description;

    /** @var array */
    public $available_services;

    /**
     * Create a new Calendar Item instance
     *
     * @param mixed $date
     * @param string $interval
     * @param App\Salon\Employee $professional
     * @param App\Salon\Client\BookingCollection $bookings
     */
    public function __construct(Carbon $date, string $interval, Employee $professional, BookingCollection $bookings)
    {
        $this->validate($professional, $bookings);

        $this->id = strip_non_alphanumeric($date->timestamp.$interval.$professional->id);
        $this->date = $date;
        $this->interval = $interval;
        $this->professional = $professional;
        $this->bookings = $bookings;

        $this->computeAvailableServicesAttribute();

        $this->computeBlockedAttribute();

        $this->computeDescriptionAttribute();
    }

    /**
     * Returns if this Calendar Item has the given Professional Employee
     *
     * @param Employee $professional
     *
     * @return bool
     */
    public function hasProfessional(Employee $professional) : bool
    {
        return $this->professional->is($professional);
    }

    /**
     * Returns if this model is registered on the given date
     *
     * @param mixed $date
     *
     * @return bool
     */
    public function isOnDate($date) : bool
    {
        return $this->date->equalTo(Carbon::parse($date)->startOfDay());
    }

    /**
     * Compute the available_services attribute of this the Calendar Item
     *
     * @return void
     */
    protected function computeAvailableServicesAttribute() : void
    {
        $this->available_services = $this->professional->services->filter(function ($service) {
            return $this->professional->canHaveNewBookingWith($this->date, $this->interval, $service);
        });
    }

    /**
     * Compute the blocked attribute of this the Calendar Item
     *
     * @return void
     */
    protected function computeBlockedAttribute() : void
    {
        $this->blocked = ! $this->professional->services->contains(function ($service) {
            return $this->professional->canHaveNewBookingWith($this->date, $this->interval, $service);
        });
    }

    /**
     * Compute the description attribute of this the Calendar Item
     *
     * @return void
     */
    protected function computeDescriptionAttribute() : void
    {
        try {
            $this->professional->services->each(function ($service) {
                $this->professional->validateNewBookingWith($this->date, $this->interval, $service);
            });
        } catch (CalendarIntervalFull $e) {
            $this->description = __('Full Calendar Item');
            return;
        }

        $this->description = __('Available');
    }
    /**
     * Returns if this Calendar Item is registered on the given calendar interval
     *
     * @param string $interval
     *
     * @return bool
     */
    public function isOnInterval(string $interval) : bool
    {
        return Carbon::parse($this->interval)->equalTo(Carbon::parse($interval));
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'date' => $this->date->toDateString(),
            'interval' => $this->interval,
            'professional' => $this->professional->makeHidden(['bookings', 'services'])->toArray(),
            'bookings' => $this->bookings->makeHidden('professional')->toArray(),
            'blocked' => $this->blocked,
            'description' => $this->description,
            'available_services' => $this->available_services->pluck('id')->toArray(),
        ];
    }

    /**
     * Convert the Calendar Item item instance to JSON.
     *
     * @param  int  $options
     * @return string
     *
     * @throws RuntimeException
     */
    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $class = get_class($this);
            $error = json_last_error_msg();
            throw new RuntimeException("Error encoding calendar item [$class] to JSON: $error");
        }

        return $json;
    }

    /**
     * Convert the Celendar Item into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }

    /**
     * Validate the given arguments
     *
     * @param App\Salon\Employee $professional
     * @param App\Salon\Client\BookingCollection $bookings
     *
     * @return void
     */
    protected function validate(Employee $professional, BookingCollection $bookings) : void
    {
        if (! $professional->isProfessional()) {
            throw new InvalidArgumentException('Calendar Item must be constructed using only a Salon Professionals.');
        }

        if ($bookings->pluck('professional_id')->diff($professional->id)->isNotEmpty()) {
            throw new InvalidArgumentException('Bookings of Calendar Item must belongs to the given Professional');
        }
    }
}
