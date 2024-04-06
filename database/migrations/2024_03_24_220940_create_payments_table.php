<?php

use App\Enums\PaymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE TYPE payment_status AS ENUM ('pending', 'completed', 'failed', 'refunded')");

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('subscription_id')->constrained('subscriptions');
            $table->decimal('amount');
            $table->string('transaction_id', 150);
            $table->string('additional_transaction_id', 150);
            $table->jsonb('payload');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE payments ADD COLUMN status payment_status NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        DB::statement('DROP TYPE IF EXISTS payment_status');

        Schema::dropIfExists('payments');
    }
};
