<?php

namespace App\Http\Controllers\Api\References;

use App\Enums\TemplateStatus;
use App\Http\Requests\Api\Template\TemplateCreateRequest;
use App\Http\Requests\Api\Template\TemplateUpdateRequest;
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

    /**
     * @param Template $template
     * @return JsonResponse
     */
    public function show(Template $template): JsonResponse
    {
        $template->load(['translationDirection.sourceLanguage', 'translationDirection.targetLanguage']);

        $template_array = $template->toArray();

        $template_array['source_language'] = $template->translationDirection->sourceLanguage->toArray();
        $template_array['target_language'] = $template->translationDirection->targetLanguage->toArray();

        $this->setResponse($template_array);
        return $this->sendResponse();
    }

    public function update(TemplateCreateRequest $request, Template $template): JsonResponse
    {
        $validated_data = $request->validated();
        $template->update($validated_data);

        $this->setResponse($template->toArray());
        return $this->sendResponse();
    }

    public function create(TemplateCreateRequest $request): JsonResponse
    {
        $validated_data = $request->validated();
        $template = Template::create($validated_data);

        $this->setResponse($template->toArray());
        return $this->sendResponse();
    }
}
