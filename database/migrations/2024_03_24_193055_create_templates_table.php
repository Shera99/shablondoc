<?php

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
        DB::statement("CREATE TYPE template_status AS ENUM ('active', 'inactive', 'moderation')");

        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->nullable(false);
            $table->jsonb('template_json')->nullable(false);
            $table->string('template_file', 255)->nullable();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('document_type_id')->nullable()->constrained('document_types')->restrictOnDelete();
            $table->string('new_document_type', 200)->nullable();
            $table->foreignId('translation_direction_id')->constrained('translation_directions');
            $table->string('email', 150);
            $table->boolean('payed_status')->default(false);
            $table->string('code', 255);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE templates ADD COLUMN status template_status NOT NULL DEFAULT 'moderation'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        DB::statement('DROP TYPE IF EXISTS template_status');
        Schema::dropIfExists('templates');
    }
};
