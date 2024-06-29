<?php

namespace App\Http\Controllers\Api\References;

use App\Http\Controllers\Controller;
use App\Models\CertificationSignatureType;
use Illuminate\Http\JsonResponse;

class CertificationSignatureTypeController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $certification_signature_types = CertificationSignatureType::orderBy('name', 'asc')->get(['id', 'name'])->toArray();
        $this->setResponse($certification_signature_types);

        return $this->sendResponse();
    }
}
