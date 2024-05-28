<?php

namespace App\Http\Requests\Api\Employee;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class EmployeeCreateRequest extends BaseRequest
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
            'password' => 'required|min:8',
            'password_confirmation' => 'required|min:8|same:password',
            'company_id' => [
                'required',
                'int',
                Rule::exists('companies', 'id')
            ],
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
            'role.required' => 'The role field is required.',
            'role.string' => 'The role must be a string.',
            'email.required' => 'The email field is required.',
            'email.string' => 'The email must be a string.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least :min characters.',
            'password_confirmation.required' => 'The password confirmation field is required.',
            'password_confirmation.min' => 'The password confirmation must be at least :min characters.',
            'password_confirmation.same' => 'The password confirmation must match the password.',
            'company_id.required' => 'The company ID field is required.',
            'company_id.int' => 'The company ID must be an integer.',
            'company_id.exists' => 'The selected company ID does not exist.',
        ];
    }
}
