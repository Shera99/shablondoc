<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StandardAndCorporateRoleAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user->hasRole('Standard') || $user->hasRole('Corporate')) return $next($request);

        return \response()->json(['success' => false, 'data' => [], 'message' => 'Access denied: insufficient permissions.'])
            ->setStatusCode(Response::HTTP_FORBIDDEN);
    }
}
