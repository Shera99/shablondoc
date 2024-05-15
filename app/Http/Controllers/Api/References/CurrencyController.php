<?php

namespace App\Http\Controllers\Api\References;

use App\Models\Country;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;

class CurrencyController extends \App\Http\Controllers\Controller
{
    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $currencies = Currency::query()->where('status', true)->get(['id', 'name', 'code'])->toArray();
        $this->setResponse($currencies);

        return $this->sendResponse();
    }
}
