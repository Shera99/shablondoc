<?php

namespace App\Http\Services;

use App\Enums\OrderStatus;
use App\Http\Modules\Image;
use App\Models\Order;
use Symfony\Component\HttpFoundation\Response;

class OrderService
{
    protected Order $order;

    public function __construct()
    {
        $this->order = app(Order::class);
    }

    public function create(array $request_data, $request)
    {
        $this->order->phone_number = $request_data['phone_number'];
        $this->order->company_address_id = $request_data['address_id'];
        $this->order->delivery_date = $request_data['delivery_date'];
        $this->order->delivery_time = $request_data['delivery_time'];

        if (!empty($request_data['template_id'])) $this->order->template_id = $request_data['template_id'];
        elseif ($request->hasFile('document_image')) {
            $image = $request->file('document_image');
            $image_save_result = Image::save($image);

            if (in_array('error', $image_save_result)) return $image_save_result;

            $this->order->document_file = $image_save_result['storedImagePath'];
            $this->order->document_name = $request_data['document_name'];
            $this->order->country_id = $request_data['country_id'];
            $this->order->language_id = $request_data['language_id'];
        } else {
            return ['error' => Response::HTTP_BAD_REQUEST, 'message' => 'Document image is required.'];
        }

        if (!empty($request_data['email'])) $this->order->email = $request_data['email'];
        if (!empty($request_data['comment'])) $this->order->comment = $request_data['comment'];

        $this->order->status = OrderStatus::PENDING;
        $this->order->save();

        return $this->order;
    }
}
