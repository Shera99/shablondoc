<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $validate_data = $request->validated();

        $user = User::create([
            'login' => $validate_data['name'],
            'email' => $validate_data['email'],
            'password' => Hash::make($validate_data['password']),
        ]);

        $user->assignRole($validate_data['role']);

        $this->setResponse([
            'access_token' => $user->createToken($user->email.'-AuthToken')->plainTextToken,
        ], Response::HTTP_CREATED);

        return $this->sendResourceResponse(new UserResource(), $user);
    }
}
