<?php

namespace App\Http\Controllers\Api\Order;

use App\Enums\OrderStatus;
use App\Events\NewOrder;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\Template;
use App\Models\TemplateData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\Order\{OrderCreateRequest,
    OrderSetWebCallBackRequest,
    OrderTranslateRequest,
    OrderUserLinkRequest};
use App\Http\Services\OrderService;
use App\Http\Services\PaymentService;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

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

        $sum = (int) Setting::query()->where('key', 'order_price')->value('value');
        if (!empty($order->mynumer)) {
            $discount = (int) Setting::query()->where('key', 'mynumer_discount')->value('value');
            $sum = $sum - ($sum / 100 * $discount);
        }

        $user_id = !empty($order->user_id) ? $order->user_id : 0;

        $result = $this->payment_service->create($order->id, $sum, 'KGS',  'order', $user_id);
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

    public function list(Request $request): \Illuminate\Http\JsonResponse
    {
        $orders = $this->service->list($request, ['completed'], 'completed');
        $this->setResponse($orders);
        return $this->sendResponse();
    }

    public function myOrders(Request $request): \Illuminate\Http\JsonResponse
    {
        $orders = $this->service->list($request, ['completed'], 'translated');
        $this->setResponse($orders);
        return $this->sendResponse();
    }

    public function myDelivaries(Request $request): \Illuminate\Http\JsonResponse
    {
        $orders = $this->service->list($request, ['translated', 'delivery', 'delivered'], 'delivered');
        $this->setResponse($orders);
        return $this->sendResponse();
    }

    public function userLink(Order $order, OrderUserLinkRequest $request): \Illuminate\Http\JsonResponse
    {
        $order->user_id = $request->user_id;
        $order->save();

        return $this->responseOrder($order);
    }

    public function print(Order $order): \Illuminate\Http\JsonResponse
    {
        $order->load(['companyAddress.company.user']);

        $user = $order->companyAddress->company->user;

        $subscription = $user->userSubscription()
            ->where('is_active', true)
            ->whereColumn('count_translation', '>', 'used_count_translation')
            ->first();

        if (!$subscription) {
            return $this->sendErrorResponse('No valid subscription found', Response::HTTP_BAD_REQUEST);
        }

        $subscription->used_count_translation++;
        $subscription->save();

        $order->print_date = Carbon::now();
        $order->save();

        return $this->responseOrder($order);
    }

    public function show(Order $order): \Illuminate\Http\JsonResponse
    {
        return $this->responseOrder($order);
    }

    public function linkTemplate(Order $order, Template $template): \Illuminate\Http\JsonResponse
    {
        $order->template_id = $template->id;
        $order->save();

        return $this->responseOrder($order);
    }

    public function translate(OrderTranslateRequest $request, Order $order): \Illuminate\Http\JsonResponse
    {
        if ($request->exists('template_data_id') && $request->get('template_data_id')) {
            $order->template_data_id = $request->get('template_data_id');
            $template_data = TemplateData::where('id', $order->template_data_id)->first();
            $template_data->data_json = $request->get('template_data');
            $template_data->save();
        } else {
            $template_data = app(TemplateData::class);
            $template_data->data_json = $request->get('template_data');
            $template_data->save();
            $order->template_data_id = $template_data->id;
        }

        if ($request->exists('certification_signature_id') && $request->get('certification_signature_id'))
            $order->certification_signature_id = $request->get('certification_signature_id');

        $order->status = OrderStatus::TRANSLATED;
        $order->updated_at = Carbon::now();

        $order->save();

        broadcast(new NewOrder('new-delivery'))->toOthers();

        return $this->responseOrder($order);
    }

    public function filesDownload(Order $order)
    {
        $files = $order->document_file;

        foreach ($files as $filename) {
            if (!Storage::disk('public')->exists($filename)) {
                return response()->json(['error' => 'File not found: ' . $filename], Response::HTTP_NOT_FOUND);
            }
        }

        // Создайте zip-архив для скачивания нескольких файлов
        $zip_file_name = 'files_' . time() . '.zip';
        $storage_path = 'public/images/zip/';

        if (!Storage::exists($storage_path)) {
            Storage::makeDirectory($storage_path);
        }

        $zip = new ZipArchive();
        $zip_file_path = storage_path('app/' . $storage_path . $zip_file_name);

        // Откройте zip-архив
        if ($zip->open($zip_file_path, ZipArchive::CREATE) !== true) {
            return response()->json(['error' => 'Failed to create zip file'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Добавьте файлы в архив
        foreach ($files as $file_path) {
            $file_name = basename($file_path); // Имя файла без пути
            $zip->addFile(Storage::disk('public')->path($file_path), $file_name);
        }

        $zip->close();

        return response()->download(storage_path('app/' . $storage_path . $zip_file_name))->deleteFileAfterSend();
    }

    private function responseOrder(Order $order): \Illuminate\Http\JsonResponse
    {
        $order->load([
            'user',
            'template',
            'template.translationDirection.sourceLanguage',
            'template.translationDirection.targetLanguage',
            'templateData',
            'companyAddress',
            'country',
            'language',
            'certificationSignature'
        ]);

        $this->setResponse($order->toArray());
        return $this->sendResponse();
    }
}
