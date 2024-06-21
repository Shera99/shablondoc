<?php

namespace App\Http\Requests\Api\Template;

use Illuminate\Foundation\Http\FormRequest;

class TemplateCreateRequest extends FormRequest
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
            'template_json' => 'required|json',
            'country_id' => 'required|int|exists:countries,id',
            'document_type_id' => 'required|int|exists:document_types,id',
            'translation_direction_id' => 'required|int|exists:translation_directions,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'template_json.required' => 'The template JSON field is required.',
            'template_json.json' => 'The template JSON must be a valid JSON string.',
            'country_id.required' => 'The country field is required.',
            'country_id.int' => 'The country field must be an integer.',
            'country_id.exists' => 'The selected country is invalid.',
            'document_type_id.required' => 'The document type field is required.',
            'document_type_id.int' => 'The document type field must be an integer.',
            'document_type_id.exists' => 'The selected document type is invalid.',
            'translation_direction_id.required' => 'The translation direction field is required.',
            'translation_direction_id.int' => 'The translation direction field must be an integer.',
            'translation_direction_id.exists' => 'The selected translation direction is invalid.',
        ];
    }
}
