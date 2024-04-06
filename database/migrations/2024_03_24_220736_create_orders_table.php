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
        DB::statement("CREATE TYPE order_status AS ENUM ('pending', 'completed', 'translation', 'delivery', 'delivered')");

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('template_id')->constrained('templates');
            $table->foreignId('template_data_id')->constrained('template_data');
            $table->foreignId('company_id')->constrained('companies');
            $table->string('document_file', 255);
            $table->date('delivery_date');
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
