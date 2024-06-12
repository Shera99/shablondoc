<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends \App\Http\Controllers\Controller
{
    protected PaymentService $service;

    public function __construct()
    {
        $this->service = app(PaymentService::class);
    }

    public function callBack(Request $request, string $secret): \Illuminate\Http\JsonResponse
    {
        Log::channel('http')->info('Уведомление о платеже - ' . json_encode($request->all()));

        if ($secret !== config('app.payment_key'))
            return $this->sendErrorResponse('Incorrect access key!', Response::HTTP_UNAUTHORIZED);

        $this->service->callBack($request->all());

        return $this->sendResponse();
    }
}
