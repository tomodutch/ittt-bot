<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trigger_executions', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("trigger_id")->constrained("triggers")->cascadeOnDelete();
            $table->string("origin_type", 55)->nullable();
            $table->string("origin_id", 55)->nullable();
            $table->string("status_code", 55);
            $table->string("run_reason_code", 55);
            $table->json("context");
            $table->timestampTz("finished_at")->nullable();
            $table->softDeletesTz();
            $table->timestampsTz();
        });

        Schema::create('schedule_trigger_execution', function (Blueprint $table) {
            $table->uuid('trigger_execution_id');
            $table->uuid('schedule_id');

            $table->primary(['trigger_execution_id', 'schedule_id']);

            $table->foreign('trigger_execution_id')
                ->references('id')->on('trigger_executions')
                ->onDelete('cascade');

            $table->foreign('schedule_id')
                ->references('id')->on('schedules')
                ->onDelete('cascade');

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("schedule_trigger_execution");
        Schema::dropIfExists('trigger_executions');
    }
};
