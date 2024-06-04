<?php

namespace App\Http\Requests\Api\Subscription;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class SubscriptionBuyRequest extends BaseRequest
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
            'subscription_id' => [
                'required',
                'int',
                Rule::exists('subscriptions', 'id')
            ],
            'currency' => 'required|string'
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
            'subscription_id.required' => 'Subscription ID number is required.',
            'subscription_id.int' => 'Subscription ID must be an integer.',
            'subscription_id.exists' => 'The provided subscription does not exist in our records.',
            'currency.required' => 'Currency number is required.',
            'currency.string' => 'Currency number must be a string.',
        ];
    }
}
