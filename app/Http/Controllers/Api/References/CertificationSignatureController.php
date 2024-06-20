<?php

namespace App\Http\Controllers\Api\References;

use App\Http\Requests\Api\Certification\CertificationSignatureCU;
use App\Http\Services\CertificationService;
use App\Models\CertificationSignature;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CertificationSignatureController extends \App\Http\Controllers\Controller
{
    private CertificationService $service;

    public function __construct()
    {
        $this->service = app(CertificationService::class);
    }

    /**
     * List certification signatures.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        if (auth()->user()->hasRole('Employee')) {
            $companies_ids = Employee::where('user_id', auth()->user()->id)->pluck('company_id');
        } else {
            $companies_ids = auth()->user()->companies->pluck('id');
        }

        $certification_signatures = CertificationSignature::with(['certificationSignatureType', 'country', 'city', 'company', 'language'])
            ->whereIn('company_id', $companies_ids)->where('is_deleted', false)
            ->paginate(15)->toArray();

        $this->setResponse($certification_signatures);
        return $this->sendResponse();
    }

    /**
     * Create a new certification signature.
     *
     * @param CertificationSignatureCU $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(CertificationSignatureCU $request): JsonResponse
    {
        $response = $this->service->formatDataAndSaveImage($request);

        if (in_array('error', $response))
            return $this->sendErrorResponse($response['message'], $response['error']);

        $certification_signature = CertificationSignature::create($response);

        $this->setResponse($certification_signature->toArray());
        return $this->sendResponse();
    }

    /**
     * Update an existing certification signature.
     *
     * @param CertificationSignatureCU $request
     * @param CertificationSignature $certification_signature
     * @return JsonResponse
     */
    public function update(CertificationSignatureCU $request, CertificationSignature $certification_signature): JsonResponse
    {
        $response = $this->service->formatDataAndSaveImage($request);

        if (in_array('error', $response))
            return $this->sendErrorResponse($response['message'], $response['error']);

        $certification_signature->update($response);

        $this->setResponse($certification_signature->toArray());
        return $this->sendResponse();
    }

    /**
     * Delete a certification signature.
     *
     * @param CertificationSignature $certification_signature
     * @return JsonResponse
     */
    public function delete(CertificationSignature $certification_signature): JsonResponse
    {
        $certification_signature->is_deleted = true;
        $certification_signature->save();

        return response()->json([
            'success' => true,
            'message' => 'Certification signature deleted successfully.'
        ]);
    }
}
