<?php

namespace App\Http\Services;

use App\Enums\OrderStatus;
use App\Models\Payment;

class PaymentService
{
    private Payment $payment;

    public function __construct()
    {
        $this->payment = app(Payment::class);
    }

    public function create(int $foreign_id, int $amount, string $type, int $user_id = 0): array
    {
        try {
            if ($user_id !== 0) $this->payment->user_id = $user_id;
            $this->payment->foreign_id = $foreign_id;
            $this->payment->amount = $amount;
            $this->payment->type = $type;

            $this->payment->save();

            return ['payment_id' => $this->payment->id, 'amount' => $this->payment->amount];
        } catch (\Throwable $exception) {
            return ['message' => $exception->getMessage(), 'error' => $exception->getCode()];
        }
    }
}
