<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Requests\Api\Order\{OrderCreateRequest,OrderSetWebCallBackRequest};
use App\Http\Services\OrderService;
use App\Http\Services\PaymentService;
use App\Models\Order;

class OrderController extends \App\Http\Controllers\Controller
{
    private OrderService $service;
    private PaymentService $payment_service;

    public function __construct()
    {
        $this->service = app(OrderService::class);
        $this->payment_service = app(PaymentService::class);
    }

    public function create(OrderCreateRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();

        $order = $this->service->create($data, $request);
        if (!$order instanceof Order) return $this->sendErrorResponse($order['message'], $order['error']);

        $result = $this->payment_service->create($order->id, 127, $data['currency'],  'order');
        if (in_array('error', $result)) return $this->sendErrorResponse($result['message'], $result['error']);

        $this->setResponse(data: $result, message: 'Order is created.');
        return $this->sendResponse();
    }

    public function webCallBack(OrderSetWebCallBackRequest $request): \Illuminate\Http\JsonResponse
    {
        $request_data = $request->validated();
        $result = $this->payment_service->setTransaction($request_data['order_id'], $request_data['transaction_id']);

        if (in_array('error', $result)) return $this->sendErrorResponse($result['message'], $result['error']);

        $this->setResponse(message: $result['message']);
        return $this->sendResponse();
    }
}
