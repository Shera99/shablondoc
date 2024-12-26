<?php

namespace App\Http\Controllers\Api\References;

use App\Enums\TemplateStatus;
use App\Http\Modules\FileHandler;
use App\Http\Requests\Api\Template\TemplateCreateRequest;
use App\Models\Template;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TemplateController extends \App\Http\Controllers\Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $templates = Template::query()
            ->where('status', TemplateStatus::ACTIVE)
            ->where('country_id', $request->get('country_id'))
            ->where('document_type_id', $request->get('document_type_id'))
            ->where('translation_direction_id', $request->get('language_id'))
            ->orderBy('name', 'asc')
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
//        $validated_data['code'] = strtotime('now');
        $template = Template::create($validated_data);

        $this->setResponse($template->toArray());
        return $this->sendResponse();
    }

    /**
     * @throws \Exception
     */
    public function imageSave(Request $request): JsonResponse
    {
        if ($request->hasFile('image')) {
            $files = $request->file('image');

            if (!is_array($files)) {
                $files = [$files];
            }

            $image_save_result = [];

            foreach ($files as $file) {
                $image_save_result = FileHandler::save($file, 'template');
            }

            $this->setResponse(['image' => $image_save_result['storedFilePath']]);
            return $this->sendResponse();
        }

        return $this->sendErrorResponse('Image is required.', Response::HTTP_BAD_REQUEST);
    }
}
