<?php

namespace App\Http\Requests\Api\Order;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class OrderTranslateRequest extends BaseRequest
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
            'template_data_id' => 'nullable|int|exists:template_data,id',
            'certification_signature_id' => 'nullable|int|exists:certification_signatures,id',
            'template_data' => 'required|json',
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
            'template_data.required' => 'Template data is required.',
            'template_data.json' => 'Template data must be a json.',
            'template_data_id.int' => 'Template data ID must be an integer.',
            'template_data_id.exists' => 'The selected template data ID does not exist.',
            'certification_signature_id.int' => 'Certification signature ID must be an integer.',
            'certification_signature_id.exists' => 'The selected certification signature ID does not exist.',
        ];
    }
}
