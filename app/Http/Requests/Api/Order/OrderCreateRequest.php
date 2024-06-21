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
            'phone_number' => 'required|string',
            'address_id' => 'required|int',
            'delivery_date' => 'required|date',
            'template_id' => 'nullable|int',
            'document_image' => 'required|file',
            'document_name' => 'nullable|string',
            'email' => 'nullable|string|email',
            'comment' => 'nullable|string',
            'country_id' => 'nullable|int',
            'language_id' => 'nullable|int',
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
        ];
    }
}
