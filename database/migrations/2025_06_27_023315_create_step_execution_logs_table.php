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
        Schema::create('step_execution_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('trigger_execution_id')->constrained('trigger_executions')->cascadeOnDelete();
            $table->foreignUuid('step_id')->constrained('steps')->cascadeOnDelete();
            $table->string('level');
            $table->string('message');
            $table->json('details')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('step_execution_logs');
    }
};
