<?php

namespace App\Http\Controllers\Api\References;

use App\Http\Requests\Api\Subscription\SubscriptionBuyRequest;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends \App\Http\Controllers\Controller
{
    public function list(): JsonResponse
    {
        $activeSubscriptions = Subscription::where('is_active', true)
            ->get()->toArray();

        $this->setResponse($activeSubscriptions);

        return $this->sendResponse();
    }

    public function buy(SubscriptionBuyRequest $request)
    {

    }
}
