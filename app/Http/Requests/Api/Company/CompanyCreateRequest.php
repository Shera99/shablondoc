<?php

namespace App\Http\Requests\Api\Company;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class CompanyCreateRequest extends BaseRequest
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
            'country_id' => [
                'required',
                'int',
                Rule::exists('countries', 'id')
            ],
            'company_type_id' => [
                'required',
                'int',
                Rule::exists('company_types', 'id')
            ]
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
            'country_id.exists' => 'The provided countries does not exist in our records.',
            'company_type_id.required' => 'The company type field is required.',
            'company_type_id.int' => 'The company type must be an integer.',
            'company_type_id.exists' => 'The selected company type is invalid.'
        ];
    }
}
