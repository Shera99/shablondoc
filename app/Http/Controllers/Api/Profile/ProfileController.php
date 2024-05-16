<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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

    public function show(User $user): JsonResponse
    {
        $this->setResponse();
        return $this->sendResourceResponse(new UserResource(), $user);
    }
}
