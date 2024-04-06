<?php

namespace App\Http\Controllers\Api\References;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\JsonResponse;

class DocumentTypeController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $document_types = DocumentType::all(['id', 'name'])->toArray();
        $this->setResponse($document_types);

        return $this->sendResponse();
    }
}
