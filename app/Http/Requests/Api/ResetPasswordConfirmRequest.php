<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ResetPasswordConfirmRequest extends BaseRequest
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
            'email' => [
                'required',
                'string',
                'email',
                Rule::exists('users', 'email')
            ],
            'password' => 'required|min:8',
            'password_confirmation' => 'required|min:8|same:password',
            'code' => 'required|string|size:10',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'The email field is required.',
            'email.string' => 'The email must be a string.',
            'email.email' => 'The email must be a valid email address.',
            'email.exists' => 'The provided email does not exist in our records.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least :min characters.',
            'password_confirmation.required' => 'The password confirmation field is required.',
            'password_confirmation.min' => 'The password confirmation must be at least :min characters.',
            'password_confirmation.same' => 'The password confirmation must match the password.',
            'code.required' => 'The code field is required.',
            'code.string' => 'The code must be a string.',
            'code.size' => 'The code must be :size characters long.',
        ];
    }
}
