<?php

namespace App\Http\Services;

use App\Enums\OrderStatus;
use App\Http\Modules\FileHandler;
use App\Models\Order;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class OrderService
{
    protected Order $order;

    public function __construct()
    {
        $this->order = app(Order::class);
    }

    /**
     * @throws \Exception
     */
    public function create(array $request_data, Request $request)
    {
//        dd($request->file('files'));
        $this->order->phone_number = $request_data['phone_number'];
        $this->order->delivery_date = $request_data['delivery_date'];

        if (!empty($request_data['template_id'])) $this->order->template_id = $request_data['template_id'];
        elseif (isset($request_data['document_name']) && !empty($request_data['document_name'])) {
            $this->order->document_name = $request_data['document_name'];
            $this->order->country_id = $request_data['country_id'];
            $this->order->language_id = $request_data['language_id'];
        } else {
            return ['error' => Response::HTTP_BAD_REQUEST, 'message' => 'Document data is required.'];
        }

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $paths = [];

            foreach ($files as $key => $file) {
                try {
                    $result = FileHandler::save($file, 'order');
                    $paths[] = $result['storedFilePath'];
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'errors' => ['message' => $e->getMessage()]])
                        ->setStatusCode(Response::HTTP_BAD_REQUEST);
                }
            }

            $this->order->document_file = $paths;
        } else {
            return ['error' => Response::HTTP_BAD_REQUEST, 'message' => 'Document(s) is required.'];
        }

        if (!empty($request_data['email'])) $this->order->email = $request_data['email'];
        if (!empty($request_data['comment'])) $this->order->comment = $request_data['comment'];
        if (!empty($request_data['mynumer'])) $this->order->mynumer = $request_data['mynumer'];
        if (!empty($request_data['address_id'])) $this->order->company_address_id = $request_data['address_id'];
        if (!empty($request_data['user_id'])) $this->order->user_id = $request_data['user_id'];

        $this->order->status = !empty($request_data['user_id']) ? OrderStatus::COMPLETED : OrderStatus::PENDING;
        $this->order->save();

        return $this->order;
    }
}
