<?php

namespace App\Http\Controllers\Api\References;

use App\Helpers\ApiHelper;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class CurrencyController extends \App\Http\Controllers\Controller
{
    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $currencies = Currency::query()->where('status', true)
            ->get(['id', 'name', 'code', 'convert'])->toArray();
        $this->setResponse($currencies);

        return $this->sendResponse();
    }

    public function amount(): JsonResponse
    {
        $amount = (int) Setting::query()->where('key', 'order_price')->value('value');
        $converted_amount = ApiHelper::getConvertedAmount('KGS', $amount);
        $this->setResponse(['amount' => $converted_amount]);
        return $this->sendResponse();
    }
}
