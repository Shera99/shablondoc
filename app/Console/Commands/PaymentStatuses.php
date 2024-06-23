<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Services\PaymentService;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PaymentStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:payment-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check payment statuses every 30 minutes';

    /**
     * @return void
     */
    public function handle()
    {
        $start = Carbon::now()->subHours(8)->format('Y-m-d H:i:s');

        $payments = Payment::where('status', PaymentStatus::PENDING)->whereDate('created_at', '>', $start)->get();

        $paymentService = new PaymentService();

        foreach ($payments as $payment) {
            try {
                $paymentService->setPayment($payment);
                $paymentService->checkStatus();
                $payment->save();
            } catch (\Exception $e) {
                Log::error('Payment status check failed for payment ID ' . $payment->id . ': ' . $e->getMessage());
            }
        }
    }
}
