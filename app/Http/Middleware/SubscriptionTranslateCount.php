<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionTranslateCount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user->hasRole('Employee')) {
            $user = $user->employee->getCompanyUser();
        }

        $subscription = $user->userSubscription()
            ->where('is_active', true)
            ->whereDate('subscription_date', '<=', Carbon::now())
            ->whereDate('subscription_end_date', '>=', Carbon::now())
            ->first();

        if ($subscription && $subscription->count_translation > $subscription->used_count_translation) return $next($request);

        return \response()->json(['success' => false, 'data' => [], 'message' => 'You have reached the limit on the number of translations.'])
            ->setStatusCode(Response::HTTP_FORBIDDEN);
    }
}
