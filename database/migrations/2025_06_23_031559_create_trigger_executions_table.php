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
        Schema::create('trigger_executions', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("trigger_id")->constrained("triggers")->cascadeOnDelete();
            $table->foreignUuid("schedule_id")->nullable()->constrained("schedules")->cascadeOnDelete();
            $table->string("origin_type", 55)->nullable();
            $table->string("origin_id", 55)->nullable();
            $table->smallInteger("status_code");
            $table->smallInteger("run_reason_code");
            $table->json("context");
            $table->timestampTz("finished_at")->nullable();
            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trigger_executions');
    }
};
