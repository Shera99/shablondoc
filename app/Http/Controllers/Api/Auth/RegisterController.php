<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\UserService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    protected UserService $userService;

    public function __construct()
    {
        $this->userService = app(UserService::class);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $validate_data = $request->validated();

        $user = $this->userService->create($validate_data);

        $this->setResponse([
            'access_token' => $user->createToken($user->email.'-AuthToken')->plainTextToken,
        ], Response::HTTP_CREATED);

        return $this->sendResourceResponse(new UserResource(), $user);
    }
}
