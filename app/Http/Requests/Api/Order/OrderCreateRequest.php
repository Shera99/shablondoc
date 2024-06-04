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
            'template_id' => 'null|int',
            'document_image' => 'null|file',
            'document_name' => 'null|string',
            'email' => 'null|string|email',
            'comment' => 'null|string',
            'country_id' => 'null|int',
            'language_id' => 'null|int',
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
