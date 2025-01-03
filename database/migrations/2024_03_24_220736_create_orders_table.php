<?php

use App\Enums\OrderStatus;
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
//        DB::statement("CREATE TYPE order_status AS ENUM ('moderation', 'pending', 'completed', 'translation', 'delivery', 'delivered', 'failed', 'translate_moderation')");

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('template_id')->nullable()->constrained('templates');
            $table->foreignId('template_data_id')->nullable()->constrained('template_data');
            $table->foreignId('company_address_id')->constrained('company_addresses');
            $table->foreignId('country_id')->nullable()->constrained('countries');
            $table->foreignId('translation_direction_id')->nullable()->constrained('translation_directions');
            $table->foreignId('certification_signature_id')->nullable()->constrained('certification_signatures');
            $table->text('document_name')->nullable();
            $table->text('document_file')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone_number', 20)->nullable(false);
            $table->timestamp('delivery_date')->nullable();
            $table->timestamp('print_date')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE orders ADD COLUMN status order_status NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        DB::statement('DROP TYPE IF EXISTS order_status');

        Schema::dropIfExists('orders');
    }
};
