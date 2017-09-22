<?php

namespace App\Http\Requests\Salon;

use App\Salon\Employee;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @see Employee
 *
 * StoreEmployee determines who can store an Employee.
 * @see self::authorize
 *
 * Custom request to store Employees
 * StoreEmployee also validates the request data used to
 * store a Employee
 * @see self::validate
 *
 * @see FormRequest
 */
class StoreEmployee extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Employee::class);
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
                }),
            ],
            'password' => 'required|case_diff|numbers|max:60',
        ];
    }
}
