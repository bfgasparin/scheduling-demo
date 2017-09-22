<?php

namespace App\Http\Requests\Salon\Professional\WorkingJorney;

use Illuminate\Validation\Rule;
use App\Salon\Professional\WorkingJorney;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Custom request to update absences from a WorkingJorney's Professional
 * @see App\Salon\Professional\WorkingJorney\Absence
 *
 * StoreAbsence determines who can update absences from a WorkingJorney's Professional.
 * @see self::authorize
 *
 * StoreEmployee also validates the request data used to update a absence
 * @see self::validate
 *
 * @see FormRequest
 */
class UpdateAbsence extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('workingJorney'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => [
                'required', 'date',
                Rule::unique('working_jorney_absences')->where(function ($query) {
                    $query->where('working_jorney_id', $this->route('workingJorney')->id);
                })->ignore($this->route('absence')),
            ],
            'observation' => 'required|string',
        ];
    }
}
