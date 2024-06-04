<?php

namespace App\Http\Services;

use App\Helpers\ApiHelper;
use App\Models\UserSubscription;
use App\Enums\{PaymentStatus,OrderStatus};
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PaymentService
{
    private Payment $payment;
    private $transaction;
    private string $base_link = 'get_status3.php';
    private array $post_data;
    private string $secret;
    private string $public;

    public function __construct()
    {
        $this->payment = app(Payment::class);
        $this->secret = config('app.payment_secret');
        $this->public = config('app.payment_public');
    }

    public function create(int $foreign_id, int $amount, string $currency, string $type, int $user_id = 0): array
    {
        try {
            $new_amount = ApiHelper::getConvertedAmount($currency, $amount);
            if ($user_id !== 0) $this->payment->user_id = $user_id;
            $this->payment->foreign_id = $foreign_id;
            $this->payment->amount = $new_amount;
            $this->payment->type = $type;
            $this->payment->additional_transaction_id = Str::random(60);

            $this->payment->save();

            return ['payment_id' => $this->payment->id, 'salt' => $this->payment->additional_transaction_id, 'amount' => $this->payment->amount, 'currency' => $currency];
        } catch (\Throwable $exception) {
            return ['message' => $exception->getMessage(), 'error' => $exception->getCode()];
        }
    }

    public function setTransaction(int $order_id, int $transaction_id): array
    {
        $payment = Payment::where('foreign_id', $order_id)->where('type', 'order')->first();

        if (!$payment) return ['message' => 'Transaction not found.', 'error' => Response::HTTP_NOT_FOUND];

        $payment->transaction_id = $transaction_id;
        $payment->save();

        return ['message' => 'Transaction updated successfully.', 'success' => true];
    }

    public function callBack(array $request_data): void
    {
        Log::channel('http')->info('Уведомление о платеже - ' . json_encode($request_data));

        $this->transaction = Payment::where('transaction_id', $request_data['pg_payment_id'])->first();

        if ($this->transaction) {
            $this->checkStatus();
            $this->transaction->save();
        }
    }

    public function checkStatus(): void
    {
        $response = $this->getStatus();

        if ($response['pg_status'] == 'ok') {
            $this->transaction->payload = json_encode($response);

            if ($response['pg_payment_status'] == 'success') {
                $this->transaction->status = PaymentStatus::COMPLETED;

                if ($this->transaction->type == 'order') {
                    $order = Order::where('id', $this->transaction->foreign_id)->first();
                    $order->status = OrderStatus::TRANSLATION;
                } else {
                    $order = UserSubscription::where('id', $this->transaction->foreign_id)->first();
                    $order->is_active = true;
                }

                $order->save();
            } elseif ($response['pg_payment_status'] == 'error') {
                $this->transaction->status = PaymentStatus::FAILED;

                if ($this->transaction->type == 'order') {
                    $order = Order::where('id', $this->transaction->foreign_id)->first();
                    $order->status = OrderStatus::FAILED;
                    $order->save();
                }
            }
        }
    }

    private function getStatus(): array
    {
        $this->post_data = [
            'pg_merchant_id' => $this->public,
            'pg_payment_id' => $this->transaction->transaction_id,
            'pg_salt' => $this->transaction->additional_transaction_id,
            'pg_order_id' => $this->transaction->id
        ];
        $this->hashSign();
        return $this->httpQuery();
    }

    /**
     * * Генерация подписи
     * @return void
     */
    private function hashSign(): void
    {
        ksort($this->post_data);
        array_unshift($this->post_data, 'get_status3.php');
        $this->post_data[] = $this->secret;
        $this->post_data['pg_sig'] = md5(implode(';', $this->post_data));
        unset($this->post_data[0], $this->post_data[1]);
    }

    private function httpQuery()
    {
        try {
            $response = Http::retry(3, 3)->asForm()->post($this->base_link, $this->post_data);

            Log::channel('http')->info('Проверка статуса платежа - ' . $response);

            if ($response->serverError()) {
                $response = [
                    'pg_status' => 'error',
                    'pg_error_code' => 999,
                    'pg_error_description' => 'Не удалось подключиться к сервису по оплате КартаМир!'
                ];
            } else $response = json_decode(json_encode(simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        } catch (\Illuminate\Http\Client\RequestException $e) {
            report($e);
            $response = [
                'pg_status' => 'error',
                'pg_error_code' => $e->getCode(),
                'pg_error_description' => $e->getMessage()
            ];
            Log::channel('http')->info('Проверка статуса платежа - ' . $e->getMessage());
        }

        return $response;
    }
}
