<?php

namespace App\Http\Services;

use App\Helpers\ApiHelper;
use App\Models\UserSubscription;
use Carbon\Carbon;
use App\Enums\{PaymentStatus,OrderStatus};
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PaymentService
{
    private Payment $transaction;
    private string $base_link;
    private array $post_data;
    private string $secret;
    private int $public;

    public function __construct()
    {
        $this->secret = config('app.payment_secret');
        $this->public = intval(config('app.payment_public'));
        $this->base_link = config('app.payment_url');
    }

    public function create(int $foreign_id, int $amount, string $currency, string $type, int $user_id = 0): array
    {
        try {
            $payment = app(Payment::class);

            $new_amount = ApiHelper::getConvertedAmount($currency, $amount);
            if ($user_id !== 0) $payment->user_id = $user_id;
            $payment->foreign_id = $foreign_id;
            $payment->amount = $new_amount;
            $payment->type = $type;
            $payment->additional_transaction_id = Str::random(60);

            $payment->save();

            return ['payment_id' => strval($payment->id), 'salt' => $payment->additional_transaction_id,
                'amount' => $payment->amount, 'currency' => $currency,
                'expires_at' => Carbon::now()->addMinutes(45)->format('Y-m-d H:i:s')];
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
        $this->transaction = Payment::where('id', intval($request_data['order']))->first();

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
                    $order->status = OrderStatus::MODERATION;
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
            'pg_order_id' => strval($this->transaction->id),
            'pg_salt' => $this->transaction->additional_transaction_id,
        ];
        $this->base_link .= 'get_status3.php';
        $this->hashSign('get_status3.php');
        return $this->httpQuery();
    }

    /**
     * * Генерация подписи
     * @return void
     */
    private function hashSign(string $endpoint): void
    {
        ksort($this->post_data);
        array_unshift($this->post_data, $endpoint);
        $this->post_data[] = $this->secret;
        $this->post_data['pg_sig'] = md5(implode(';', $this->post_data));
        unset($this->post_data[0], $this->post_data[1]);
    }

    private function httpQuery()
    {
        try {
            Log::channel('http')->info('Проверка статуса платежа SEND - ' . json_encode($this->post_data));

            $response = Http::retry(3, 3)->asForm()->post($this->base_link, $this->post_data);

            Log::channel('http')->info('Проверка статуса платежа RESPONSE - ' . $response);

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

    public function setPayment(Payment $payment): void
    {
        $this->transaction = $payment;
    }
}
