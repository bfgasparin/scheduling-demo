<?php

namespace App\Http\Requests\Salon;

use App\Salon\Employee;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Custom request to update Employees
 * @see Employee
 *
 * UpdateEmployee determines who can update an Employee.
 * @see self::authorize
 *
 * UpdateEmployee also validates the request data used to
 * update a Employee
 * @see self::validate
 *
 * @see FormRequest
 */
class UpdateEmployee extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('employee'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'email' => [
                'required', 'email',
                Rule::unique('salon_employees')->where(function ($query) {
                    $query->where('salon_id', $this->user()->getSalon()->id);
                })->ignore($this->route('employee')->id),
            ],
        ];
    }
}
