<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\{ResetPasswordRequest, ResetPasswordConfirmRequest};
use App\Mail\SendResetPasswordCodeMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordController extends Controller
{
    /**
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $validate_data = $request->validated();

        $user = User::where('email', $validate_data['email'])->first();

        $code = ApiHelper::generateEmailVerificationCode(10);

        DB::table('password_reset_tokens')->insert(['email' => $validate_data['email'], 'token' => $code]);

//        Mail::to($user)->send(new SendResetPasswordCodeMail($code));

        $this->setResponse(data: [], http_status_code: Response::HTTP_OK, message: 'Send reset password code success.');

        return $this->sendResponse();
    }

    /**
     * @param ResetPasswordConfirmRequest $request
     * @return JsonResponse
     */
    public function passwordConfirmation(ResetPasswordConfirmRequest $request): JsonResponse
    {
        $validate_data = $request->validated();

        $token = DB::table('password_reset_tokens')
            ->where('email', $validate_data['email'])
            ->where('token', $validate_data['code'])
            ->first();

        if (!$token) {
            return $this->sendErrorResponse('Invalid Credentials', Response::HTTP_BAD_REQUEST);
        }

        DB::table('password_reset_tokens')->where('email', $validate_data['email'])
            ->where('token', $validate_data['code'])->delete();

        $user = User::where('email', $validate_data['email'])->first();
        $user->password = Hash::make($validate_data['password']);
        $user->save();

        $this->setResponse(data: [], http_status_code: Response::HTTP_OK, message: 'Password changed successfully');

        return $this->sendResponse();
    }
}
