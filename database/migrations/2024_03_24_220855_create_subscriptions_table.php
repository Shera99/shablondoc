<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->nullable(false);
            $table->integer('price')->nullable(false);
            $table->integer('day_count')->nullable(false);
            $table->integer('count_translation');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('subscription_id')->constrained('subscriptions');
            $table->integer('count_translation');
            $table->boolean('is_active')->default(true);
            $table->date('subscription_date');
            $table->date('subscription_end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
