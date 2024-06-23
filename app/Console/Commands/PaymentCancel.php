<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class PaymentCancel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:payment-cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check payment statuses every 30 minutes than cancel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $start = Carbon::now()->subHours(8)->format('Y-m-d H:i:s');

        $oldPaymentsIds = Payment::where('status', PaymentStatus::PENDING)
            ->whereDate('created_at', '<', $start)
            ->pluck('id');

        if ($oldPaymentsIds->isNotEmpty()) {
            Payment::whereIn('id', $oldPaymentsIds)->update(['status' => PaymentStatus::FAILED]);

            $this->updateOrdersAndSubscriptions($oldPaymentsIds);
        }
    }

    /**
     * Update related orders and subscriptions.
     *
     * @param Collection $paymentIds
     * @return void
     */
    private function updateOrdersAndSubscriptions(Collection $paymentIds): void
    {
        $orderIds = Payment::whereIn('id', $paymentIds)
            ->where('type', 'order')
            ->pluck('foreign_id');

        $subscriptionIds = Payment::whereIn('id', $paymentIds)
            ->where('type', 'subscription')
            ->pluck('foreign_id');

        if ($orderIds->isNotEmpty()) {
            Order::whereIn('id', $orderIds)->update(['status' => OrderStatus::FAILED]);
        }

        if ($subscriptionIds->isNotEmpty()) {
            UserSubscription::whereIn('id', $subscriptionIds)->update(['is_active' => false]);
        }
    }
}
