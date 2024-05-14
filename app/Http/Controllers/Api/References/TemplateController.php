<?php

namespace App\Http\Controllers\Api\References;

use App\Enums\TemplateStatus;
use App\Models\Template;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TemplateController extends \App\Http\Controllers\Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $language_id = $request->get('language_id');
        $templates = Template::query()
            ->where('status', TemplateStatus::ACTIVE)
            ->where('country_id', $request->get('country_id'))
            ->where('document_type_id', $request->get('document_type_id'))
            ->whereHas('translationDirection', function ($query) use ($language_id) {
                $query->where('target_language_id', $language_id);
            })
            ->get(['id', 'name'])->toArray();

        $this->setResponse($templates);

        return $this->sendResponse();
    }
}
