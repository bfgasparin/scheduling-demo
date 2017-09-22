<?php

namespace App\Http\Requests\Salon\Professional;

use App\Salon\Config\Booking;
use Illuminate\Validation\Rule;
use App\Salon\Professional\WorkingJorney;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Custom request to update WorkingJorney of a Professional
 *
 * UpdateWorkingJorney determines who can update a workingJorney of a Professional.
 * @see self::authorize
 *
 * UpdateEmployee also validates the request data used to update a WorkingJorneys of
 * the Professional
 * @see self::validate
 *
 * @see FormRequest
 */
class UpdateWorkingJorney extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can(
            'update',
            $this->route('professional') ? $this->route('professional') : $this->user()
        );
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
