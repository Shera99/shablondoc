<?php

namespace App\Http\Requests\Api\Order;

use App\Http\Requests\BaseRequest;

class OrderSetWebCallBackRequest extends BaseRequest
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
            'order_id' => 'required|int',
            'transaction_id' => 'required|int',
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
            'order_id.required' => 'Order ID is required.',
            'order_id.int' => 'Order ID must be an integer.',
            'transaction_id.required' => 'Transaction ID is required.',
            'transaction_id.int' => 'Transaction ID must be an integer.',
        ];
    }
}
