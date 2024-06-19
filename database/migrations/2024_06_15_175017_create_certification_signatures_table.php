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
        Schema::create('certification_signature_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->timestamps();
        });

        Schema::create('certification_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('country_id')->constrained('countries');
            $table->foreignId('city_id')->constrained('cities');
            $table->foreignId('certification_signature_type_id')->constrained('certification_signature_types');
            $table->foreignId('language_id')->constrained('languages');
            $table->string('view', 255)->nullable();
            $table->string('user', 255)->nullable();
            $table->string('file', 255)->nullable();
            $table->text('certification_text')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certification_signatures');
        Schema::dropIfExists('certification_signature_types');
    }
};
