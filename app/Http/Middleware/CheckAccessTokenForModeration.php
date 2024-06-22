<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccessTokenForModeration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->header('User'))
            return \response()->json(['success' => false, 'data' => [], 'message' => 'User not found.'])
                ->setStatusCode(Response::HTTP_FORBIDDEN);

        $user = User::where('id', $request->header('User'))->first();

        if (!$user)
            return \response()->json(['success' => false, 'data' => [], 'message' => 'User not found.'])
                ->setStatusCode(Response::HTTP_FORBIDDEN);

        if (($user->hasRole('Super-Admin') || $user->hasRole('Moderator')) && $user->status == UserStatus::ACTIVE) {
            if ($request->header('Token') == config('app.admin_secret'))
                return $next($request);

            return \response()->json(['success' => false, 'data' => [], 'message' => 'Access denied.'])
                ->setStatusCode(Response::HTTP_UNAUTHORIZED);
        }

        return \response()->json(['success' => false, 'data' => [], 'message' => 'User not found.'])
            ->setStatusCode(Response::HTTP_FORBIDDEN);
    }
}
