<?php

namespace App\Http\Requests\Salon;

use App\BelongsToSalon;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Salon\{Client\Booking, Employee, Service};

/**
 * @see Booking
 *
 * StoreBooking determines who can booking a service.
 * @see self::authorize
 *
 * Custom request to booking a service
 * StoreBooking also validates the request data used to
 * booking a service
 * @see self::validate
 *
 * @see FormRequest
 */
class StoreBooking extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Booking::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $salon = is_a($this->user(), BelongsToSalon::class)
            ? $this->user()->getSalon()
            : $this->route('salon');

        return [
            'date' => 'required|date|after_or_equal:today',
            'start' => 'required|date_format:H:i:s',
            'professional_id' => [
                'required',
                Rule::exists('salon_employees', 'id')->where(function ($query) use ($salon) {
                    $query->whereIn('id', Employee::professional()->where('salon_id', $salon->id)->pluck('id'));
                }),
            ],
            'service_id' => [
                'required',
                Rule::exists('salon_services', 'id')->where(function ($query) use ($salon) {
                    $query->whereIn('id', Service::where('salon_id', $salon->id)->pluck('id'));
                }),
            ],
        ];
    }
}
