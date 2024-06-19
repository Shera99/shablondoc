<?php

namespace App\Http\Requests\Api\Order;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class OrderUserLinkRequest extends BaseRequest
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
            'user_id' => ['required', 'int', Rule::exists('users', 'id')]
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
            'user_id.required' => 'User ID is required.',
            'user_id.int' => 'User ID must be an integer.',
            'user_id.exists' => 'The provided users does not exist in our records.'
        ];
    }
}
