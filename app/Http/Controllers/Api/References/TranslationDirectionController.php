<?php

namespace App\Http\Controllers\Api\References;

use App\Models\Language;
use App\Models\TranslationDirection;
use Illuminate\Http\JsonResponse;

class TranslationDirectionController extends \App\Http\Controllers\Controller
{
    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $translation_directions = TranslationDirection::with(['sourceLanguage', 'targetLanguage'])->get()->toArray();

        $this->setResponse($translation_directions);
        return $this->sendResponse();
    }
}
