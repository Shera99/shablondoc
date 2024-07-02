<?php

namespace App\Http\Controllers\Api\Order;

use App\Enums\OrderStatus;
use App\Models\Employee;
use App\Models\Template;
use App\Models\TemplateData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\Order\{OrderCreateRequest,
    OrderSetWebCallBackRequest,
    OrderTranslateRequest,
    OrderUserLinkRequest};
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

        $result = $this->payment_service->create($order->id, 127, 'KGS',  'order');
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
        $query = DB::table('orders as o')
            ->join('payments as p', 'o.id', '=', 'p.foreign_id')
            ->join('company_addresses as c_a', 'o.company_address_id', '=', 'c_a.id')
            ->join('companies as cm', 'c_a.company_id', '=', 'cm.id')
            ->leftJoin('templates as t', 'o.template_id', '=', 't.id')
            ->leftJoin('countries as c', 'o.country_id', '=', 'c.id')
            ->leftJoin('users as u', 'o.user_id', '=', 'u.id')
            ->where('p.type', 'order')->whereIn('o.status', ['completed', 'translated'])
            ->where('p.status', 'completed');

        $query->when(function($q) {
            return DB::raw('o.language_id IS NOT NULL');
        }, function($q) {
            $q->leftJoin('languages as l', 'o.language_id', '=', 'l.id');
        });

        $query->when(function($q) {
            return DB::raw('o.language_id IS NULL AND o.template_id IS NOT NULL');
        }, function($q) {
            $q->leftJoin('translation_directions as td', 't.translation_direction_id', '=', 'td.id')
                ->leftJoin('languages as ld', 'td.target_language_id', '=', 'ld.id');
        });

        if (auth()->user()->hasRole('Employee')) {
            $companies = Employee::where('user_id', auth()->user()->id)->pluck('company_id');
        } else {
            $companies = auth()->user()->companies->pluck('id');
        }

        $company_addresses = DB::table('company_addresses')
            ->whereIn('company_id', $companies)
            ->pluck('id');
        $query = $query->whereIn('o.company_address_id', $company_addresses);

        if ($request->get('search')) {
            $search_text = '%' . $request->get('search') . '%';
            $query = $query->where(function ($query) use($search_text) {
                return $query->where('c_a.name', 'LIKE', $search_text)
                    ->orWhere('o.document_name', 'LIKE', $search_text)
                    ->orWhere('t.name', 'LIKE', $search_text);
            });
        }

        if ($request->get('filter')) {
            $filter_array = (array)json_decode($request->get('filter'));

            if (isset($filter_array['by_date']) && !empty($filter_array['by_date'])) {
                $query = $query->orderBy('o.delivery_date', $filter_array['by_date']);
            } else if (isset($filter_array['by_employee']) && !empty($filter_array['by_employee'])) {
                $query = $query->orderBy('u.name', $filter_array['by_employee']);
            } else if (isset($filter_array['by_phone']) && !empty($filter_array['by_phone'])) {
                $query = $query->orderBy('o.phone_number', $filter_array['by_phone']);
            }
        } else {
            $query = $query->orderBy('o.id', 'desc');
        }

        $orders = $query->select(
            'o.id', 'o.user_id', 'o.template_id', 'o.template_data_id', 'o.company_address_id', 'o.country_id',
            'o.language_id', 'o.document_name', 'o.document_file', 'o.email', 'o.phone_number', 'o.delivery_date',
            'o.comment', 'o.status', 'o.created_at', 'o.print_date', 'o.updated_at',
            'c_a.name as company_address_name', 't.name as template_name', 'cm.id as company_id', 'cm.name as company_name',
            'c.name as country_name', 'l.name as language_name', 'l.name_en as language_name_en', 'u.login as translator_login',
            'u.name as translator_name', 'u.last_name as translator_last_name', 'ld.name as l_language_name', 'ld.name_en as l_language_name_en',
        )->paginate(15)->toArray();

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

        if (empty($order->print_date)) {
            $user_subscription = $order->companyAddress->company->user->userSubscription;
            $user_subscription->used_count_translation++;
            $user_subscription->save();
        }

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

        if ($request->exists('admin')) $order->status = OrderStatus::TRANSLATED;
        else $order->status = OrderStatus::TRANSLATE_MODERATION;

        $order->save();
        return $this->responseOrder($order);
    }

    private function responseOrder(Order $order): \Illuminate\Http\JsonResponse
    {
        $order->load([
            'user',
            'template',
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
