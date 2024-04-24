<?php

namespace App\Http\Requests\Api\CompanyAddress;

use App\Http\Requests\BaseRequest;

class CompanyAddressCreateRequest extends BaseRequest
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
            'name' => 'required|string',
            'company_id' => 'required|int',
            'city_id' => 'required|int',
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
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'country_id.required' => 'The country_id field is required.',
            'country_id.int' => 'The country_id must be a int.',
            'city_id.required' => 'The city_id field is required.',
            'city_id.int' => 'The city_id must be a int.',
        ];
    }
}
