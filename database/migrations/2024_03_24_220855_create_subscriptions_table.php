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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->integer('price')->nullable(false);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name_ru', 200)->nullable(false);
            $table->string('name_en', 200)->nullable(false);
            $table->text('description_ru')->nullable(false);
            $table->text('description_en')->nullable(false);
            $table->foreignId('price_id')->constrained('prices');
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
