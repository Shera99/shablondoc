<?php

use App\Http\Controllers\Api\References\{CompanyAddressController,
    CountryController,
    LanguageController,
    FaqController,
    DocumentTypeController,
    CityController,
    CompanyController,
    TemplateController};
use App\Http\Controllers\Api\Auth\{RegisterController, AuthController, ResetPasswordController};
use App\Http\Controllers\Api\Order\OrderController;
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

    Route::post('/company', [CompanyController::class, 'create']);
    Route::match(['PUT','PATCH'], '/company/{company}', [CompanyController::class, 'update']);

    Route::post('/address', [CompanyAddressController::class, 'create']);
    Route::match(['PUT','PATCH'], '/address/{companyAddress}', [CompanyAddressController::class, 'update']);
});

Route::get('/country', [CountryController::class, 'list']);
Route::get('/language', [LanguageController::class, 'list']);
Route::get('/faq', [FaqController::class, 'list']);
Route::get('/document-type', [DocumentTypeController::class, 'list']);
Route::get('/city', [CityController::class, 'list']);
Route::get('/city/by-country', [CityController::class, 'showByCountry']);
Route::get('/company', [CompanyController::class, 'list']);
Route::get('/address', [CompanyAddressController::class, 'list']);
Route::get('/template', [TemplateController::class, 'list']);

Route::post('/order', [OrderController::class, 'create']);
