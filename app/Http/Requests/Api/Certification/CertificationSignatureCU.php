<?php

namespace App\Http\Requests\Api\Certification;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class CertificationSignatureCU extends BaseRequest
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
            'company_id' => 'required|exists:companies,id',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'language_id' => 'required|exists:languages,id',
            'certification_signature_type_id' => 'required|exists:certification_signature_types,id',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,svg|max:10240', // Updated to allow image files up to 10MB
//            'user' => 'required|string',
//            'view' => 'required|string',
            'certification_text' => 'required|string',
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
            'company_id.required' => 'The company field is required.',
            'company_id.exists' => 'The selected company does not exist.',
            'country_id.required' => 'The country field is required.',
            'country_id.exists' => 'The selected country does not exist.',
            'city_id.required' => 'The city field is required.',
            'city_id.exists' => 'The selected city does not exist.',
            'language_id.required' => 'The language field is required.',
            'language_id.exists' => 'The selected language does not exist.',
            'certification_signature_type_id.required' => 'The certification signature type field is required.',
            'certification_signature_type_id.exists' => 'The selected certification signature type does not exist.',
            'file.file' => 'The uploaded file must be a file.',
            'file.mimes' => 'The file must be one of the following types: jpeg, png, jpg, svg.',
            'file.max' => 'The file must not exceed 10MB.',
//            'user.required' => 'The user field is required.',
//            'user.string' => 'The user field must be a string.',
//            'view.required' => 'The view field is required.',
//            'view.string' => 'The view field must be a string.',
            'certification_text.required' => 'The certification text field is required.',
            'certification_text.string' => 'The certification text field must be a string.',
        ];
    }

    protected function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator): void
    {
        Log::debug('Validation errors:', $validator->errors()->all());
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json(['success' => false, 'errors' => $errors], \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
