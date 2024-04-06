<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
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

        $this->setResponse(data: [], http_status_code: Response::HTTP_OK, message: 'Logout success.');

        return $this->sendResponse();
    }
}
