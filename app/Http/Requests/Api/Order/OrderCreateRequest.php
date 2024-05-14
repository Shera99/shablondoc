<?php

namespace App\Http\Requests\Api\Order;

use App\Http\Requests\BaseRequest;

class OrderCreateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone_number' => 'required|string',
            'address_id' => 'required|int',
            'delivery_date' => 'required|date',
            'delivery_time' => [
                'required',
                'string',
                'regex:/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/'
            ],
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
            'phone_number.required' => 'Phone number is required.',
            'phone_number.string' => 'Phone number must be a string.',
            'address_id.required' => 'Address ID is required.',
            'address_id.int' => 'Address ID must be an integer.',
            'delivery_date.required' => 'Delivery date is required.',
            'delivery_date.date' => 'Delivery date must be a valid date.',
            'delivery_time.required' => 'Delivery time is required.',
            'delivery_time.string' => 'Delivery time must be a string.',
            'delivery_time.regex' => 'Delivery time must be in the format HH:MM.',
        ];
    }
}
