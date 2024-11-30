<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseRequest;

class ProfileUpdateRequest extends BaseRequest
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
            'name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => [
                'required',
                'string',
                'email'
            ],
            'address' => 'nullable|string',
            'country_id' => 'nullable|int|exists:countries,id',
            'city_id' => 'nullable|int|exists:cities,id'
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
            'name.string' => 'The name must be a string.',
            'last_name.string' => 'The last name must be a string.',
            'phone_number.string' => 'The phone number must be a string.',
            'email.required' => 'The email field is required.',
            'email.string' => 'The email must be a string.',
            'email.email' => 'The email must be a valid email address.',
            'address.string' => 'The email must be a string.',
            'country_id.int' => 'The country ID must be an integer.',
            'country_id.exists' => 'The selected country does not exist.',
            'city_id.int' => 'The city ID must be an integer.',
            'city_id.exists' => 'The selected city does not exist.',
        ];
    }
}
