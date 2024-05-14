<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\RoleType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * @param AuthRequest $request
     * @return JsonResponse
     */
    public function login(AuthRequest $request): JsonResponse
    {
        $validate_data = $request->validated();

        $user = User::where('login', $validate_data['login'])->first();

        if (!$user || !Hash::check($validate_data['password'], $user->password)) {
            return $this->sendErrorResponse('Invalid Credentials', Response::HTTP_BAD_REQUEST);
        }

        if (!$user->hasRole([RoleType::CORPORATE, RoleType::STANDARD])) {
            return $this->sendErrorResponse('Access denied for this role', Response::HTTP_FORBIDDEN);
        }

        $this->setResponse([
            'access_token' => $user->createToken($user->email.'-AuthToken')->plainTextToken,
        ], Response::HTTP_OK);

        return $this->sendResourceResponse(new UserResource(), $user);
    }
}
