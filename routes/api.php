<?php

use App\Http\Controllers\Api\References\{CountryController, LanguageController, FaqController, DocumentTypeController};
use App\Http\Controllers\Api\Auth\{RegisterController, AuthController, ResetPasswordController};
use App\Http\Controllers\Api\Profile\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    echo 'Success';
});

Route::post('/register',[RegisterController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password-reset', [ResetPasswordController::class, 'resetPassword']);
Route::post('/password-reset/confirm', [ResetPasswordController::class, 'passwordConfirmation']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [ProfileController::class, 'logout']);

    Route::get('/country', [CountryController::class, 'list']);
    Route::get('/language', [LanguageController::class, 'list']);
    Route::get('/faq', [FaqController::class, 'list']);
    Route::get('/document-type', [DocumentTypeController::class, 'list']);
});
