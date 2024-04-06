<?php

namespace App\Http\Controllers\Api\References;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;

class FaqController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $faqs = Faq::all(['id', 'question', 'answer'])->toArray();
        $this->setResponse($faqs);

        return $this->sendResponse();
    }
}
