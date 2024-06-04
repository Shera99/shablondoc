<?php

namespace App\Http\Controllers\Api\References;

use App\Helpers\ApiHelper;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends \App\Http\Controllers\Controller
{
    public function list(string $currency): JsonResponse
    {
        $activeSubscriptions = Subscription::where('is_active', true)
            ->with('price')
            ->get()->toArray();

        $modifiedSubscriptions = array_map(function ($subscription) use ($currency) {
            $subscription['price'] = ApiHelper::getConvertedAmount($currency, $subscription['price']['price']);
            return $subscription;
        }, $activeSubscriptions);

        $this->setResponse($modifiedSubscriptions);

        return $this->sendResponse();
    }
}
