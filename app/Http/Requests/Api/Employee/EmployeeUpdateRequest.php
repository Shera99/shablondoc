<?php

namespace App\Http\Requests\Api\Employee;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class EmployeeUpdateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => 'required|string',
            'email' => [
                'required',
                'string',
                'email',
                Rule::unique('users', 'email')
            ],
            'company_id' => [
                'required',
                'int',
                Rule::exists('companies', 'id')
            ]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
        ];
    }
}
