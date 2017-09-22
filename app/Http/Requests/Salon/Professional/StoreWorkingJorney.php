<?php

namespace App\Http\Requests\Salon\Professional;

use App\Salon\Config\Booking;
use Illuminate\Validation\Rule;
use App\Salon\Professional\WorkingJorney;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Custom request to store WorkingJorney to a Professional
 *
 * StoreWorkingJorney determines who can store a workingJorney to a Professional.
 * @see self::authorize
 *
 * StoreEmployee also validates the request data used to store a WorkingJorneys to
 * the Professional
 * @see self::validate
 *
 * @see FormRequest
 */
class StoreWorkingJorney extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', WorkingJorney::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'entry' => 'required|date_format:H:i:s',
            'exit' => 'required|date_format:H:i:s',
            'lunch' => 'required|date_format:H:i:s',
            'days_of_week' => 'required|array',
            'calendar_interval' => [
                'required', 'integer',
                Rule::in(Booking::CALENDAR_INTERVAL_VALUES),
            ],
        ];
    }
}
