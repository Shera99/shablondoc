<?php

namespace App\Http\Controllers\Api\References;

use App\Models\Language;
use Illuminate\Http\JsonResponse;

class LanguageController extends \App\Http\Controllers\Controller
{
    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $languages = Language::all(['id', 'code', 'name', 'name_en'])->toArray();
        $this->setResponse($languages);

        return $this->sendResponse();
    }
}
