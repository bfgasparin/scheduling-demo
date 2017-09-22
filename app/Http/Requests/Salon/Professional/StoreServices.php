<?php

namespace App\Http\Requests\Salon\Professional;

use App\Salon\Employee;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Custom request to attach services to a Professional
 *
 * StoreServices determines who can attach services to a Professional.
 * @see self::authorize
 *
 * StoreEmployee also validates the request data used to
 * attach services to Professionals
 * @see self::validate
 *
 * @see FormRequest
 */
class StoreServices extends FormRequest
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
            '*.service_id' => [
                'required',
                'integer',
                Rule::exists('salon_services', 'id')->where(function ($query) {
                    $query->where('salon_id', $this->user()->getSalon()->id);
                }),
            ],
            '*.duration' => 'required|integer',
        ];
    }
}
