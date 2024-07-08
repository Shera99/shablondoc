<?php

namespace App\Http\Controllers\Api\References;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        if ($request->get('country_id')) {
            $document_types = DocumentType::where('country_id', $request->get('country_id'))->orderBy('name', 'asc')->get(['id', 'name'])->toArray();
        } else $document_types = DocumentType::orderBy('name', 'asc')->get(['id', 'name'])->toArray();
        $this->setResponse($document_types);

        return $this->sendResponse();
    }
}
