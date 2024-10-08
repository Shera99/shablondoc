<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProfileUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function websocket()
    {
        return view('welcome');
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();

        $this->setResponse(message: 'Logout successful.');

        return $this->sendResponse();
    }

    public function show(): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole('Corporate') || $user->hasRole('Standard')) {
            $subscription = $user->userSubscription()
                ->with(['subscription'])
                ->where('is_active', true)
                ->whereDate('subscription_date', '<=', Carbon::now())
                ->whereDate('subscription_end_date', '>=', Carbon::now())
                ->select('count_translation', 'used_count_translation', 'is_active', 'subscription_date', 'subscription_end_date')
                ->first();
        } else {
            $employee = $user->employee; // Get the employee instance
            if ($employee) {
                $corporate_user = $employee->getCompanyUser(); // Call the method on the employee instance
                $subscription = $corporate_user->userSubscription()->with(['subscription'])
                    ->where('is_active', true)
                    ->whereDate('subscription_date', '<=', Carbon::now())
                    ->whereDate('subscription_end_date', '>=', Carbon::now())
                    ->select('count_translation', 'used_count_translation', 'is_active', 'subscription_date', 'subscription_end_date')
                    ->first();
            } else {
                // Handle the case when the user does not have an associated employee
                $subscription = null;
            }
        }

        if ($subscription) {
            $this->setResponse($subscription->toArray());
        } else $this->setResponse([
            'count_translation' => 0,
            'used_count_translation' => 0
        ]);

        return $this->sendResourceResponse(new UserResource(), $user);
    }

    public function subscriptionTransactionList(): JsonResponse
    {
        $transactions = UserSubscription::with(['subscription', 'payment'])->where('user_id', Auth::user()->getAuthIdentifier())
            ->orderBy('id', 'desc')->get()->toArray();

        $this->setResponse($transactions);
        return $this->sendResponse();
    }

    public function update(User $user, ProfileUpdateRequest $request)
    {
        $validate_data = $request->validated();

        if (User::query()->where('id', '<>', $user->id)->where('email', $validate_data['email'])->exists())
            return $this->sendErrorResponse('The email has already been taken.', 422);

        $user->update($validate_data);

        $this->setResponse($user->toArray());
        return $this->sendResponse();
    }
}
