<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
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
        $this->setResponse();
        return $this->sendResourceResponse(new UserResource(), Auth::user());
    }
}
