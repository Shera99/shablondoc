<?php

use App\Http\Controllers\Api\Payment\PaymentController;
use App\Http\Middleware\{CorporateRoleAccessMiddleware,StandardAndCorporateRoleAccessMiddleware,SubscriptionHasActive};
use App\Http\Controllers\Api\References\{CertificationSignatureController,
    CertificationSignatureTypeController,
    CompanyAddressController,
    CountryController,
    LanguageController,
    FaqController,
    DocumentTypeController,
    CityController,
    CompanyController,
    SubscriptionController,
    TemplateController,
    CurrencyController,
    TranslationDirectionController};
use App\Http\Controllers\Api\Auth\{RegisterController, AuthController, ResetPasswordController};
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Profile\ProfileController;
use App\Http\Controllers\Api\Employee\EmployeeController;
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
    Route::post('/logout', [ProfileController::class, 'logout']);
    Route::get('/profile', [ProfileController::class, 'show']);

    Route::middleware([SubscriptionHasActive::class])->group( function () {
        Route::middleware([StandardAndCorporateRoleAccessMiddleware::class])->group(function () {
            Route::post('/certification-signature', [CertificationSignatureController::class, 'create']);
            Route::post('/certification-signature/{certification_signature}', [CertificationSignatureController::class, 'update']);
            Route::delete('/certification-signature/{certification_signature}', [CertificationSignatureController::class, 'delete']);
        });

        Route::get('/certification-signature', [CertificationSignatureController::class, 'list']);
        Route::get('/certification-signature-type', [CertificationSignatureTypeController::class, 'list']);

        Route::get('/order/{order}', [OrderController::class, 'show']);
        Route::get('/order/print/{order}', [OrderController::class, 'print']);
        Route::get('/order', [OrderController::class, 'list']);
        Route::post('/order/{order}/user-link', [OrderController::class, 'userLink']);
        Route::get('/order/{order}/template-link/{template}', [OrderController::class, 'linkTemplate']);
        Route::post('/order/{order}/translate', [OrderController::class, 'translate']);

        Route::get('/template/{template}', [TemplateController::class, 'show']);
        Route::post('/template', [TemplateController::class, 'create']);
        Route::match(['PUT', 'PATCH'], '/template/{template}', [TemplateController::class, 'update']);
    });

    Route::middleware([CorporateRoleAccessMiddleware::class])->group(function () {
        Route::get('/user/company', [CompanyController::class, 'byUser']);
        Route::post('/company', [CompanyController::class, 'create']);
        Route::match(['PUT', 'PATCH'], '/company/{company}', [CompanyController::class, 'update']);
    });

    Route::get('/address/{company}', [CompanyAddressController::class, 'byCompany']);
    Route::post('/address', [CompanyAddressController::class, 'create']);
    Route::match(['PUT', 'PATCH'], '/address/{companyAddress}', [CompanyAddressController::class, 'update']);

    Route::post('/employee', [EmployeeController::class, 'create']);
    Route::match(['PUT', 'PATCH'], '/employee/{user}', [EmployeeController::class, 'update']);
    Route::get('/employee/{company}', [EmployeeController::class, 'list']);

    Route::get('/subscription', [SubscriptionController::class, 'list']);
    Route::post('/subscription/buy', [SubscriptionController::class, 'buy']);

    Route::get('/translation-directions', [TranslationDirectionController::class, 'list']);
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
Route::get('/currency', [CurrencyController::class, 'list']);

Route::post('/order', [OrderController::class, 'create']);
Route::post('/web-call-back', [OrderController::class, 'webCallBack']);
Route::post('/payment/freedom/callback/{secret}', [PaymentController::class, 'callBack']);
