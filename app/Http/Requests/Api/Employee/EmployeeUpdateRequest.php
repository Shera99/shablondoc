<?php

namespace App\Http\Requests\Api\Employee;

use App\Http\Requests\BaseRequest;

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
                'email'
            ],
            'status' => 'required|string',
            'name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string'
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
            'login.required' => 'The login field is required.',
            'login.string' => 'The login must be a string.',
            'status.required' => 'The status field is required.',
            'status.string' => 'The status must be a string.',
            'email.required' => 'The email field is required.',
            'email.string' => 'The email must be a string.',
            'email.email' => 'The email must be a valid email address.',
        ];
    }
}
