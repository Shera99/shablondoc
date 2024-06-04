<?php

namespace App\Http\Controllers\Api\References;

use App\Http\Requests\Api\Subscription\SubscriptionBuyRequest;
use App\Http\Services\PaymentService;
use App\Models\Subscription;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionController extends \App\Http\Controllers\Controller
{
    private PaymentService $payment_service;

    public function __construct()
    {
        $this->payment_service = app(PaymentService::class);
    }

    public function list(): JsonResponse
    {
        $activeSubscriptions = Subscription::where('is_active', true)
            ->get()->toArray();

        $this->setResponse($activeSubscriptions);

        return $this->sendResponse();
    }

    public function buy(SubscriptionBuyRequest $request): JsonResponse
    {
        try {
            $validate_data = $request->validated();

            $subscription = Subscription::where('id', $validate_data['subscription_id'])->first();

            $user_subscription = app(UserSubscription::class);
            $user_subscription->user_id = Auth::id();
            $user_subscription->subscription_id = $subscription->id;
            $user_subscription->count_translation = $subscription->count_translation;
            $user_subscription->subscription_date = Carbon::now()->format("Y-m-d");
            $user_subscription->subscription_end_date = Carbon::now()->addDays($subscription->day_count)->format("Y-m-d");
            $user_subscription->is_active = false;
            $user_subscription->save();

            $result = $this->payment_service->create($user_subscription->id, $subscription->price, 'USD',  'subscription');
            if (in_array('error', $result)) return $this->sendErrorResponse($result['message'], $result['error']);

            $this->setResponse(data: $result, message: 'Subscription query is created.');
            return $this->sendResponse();
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
