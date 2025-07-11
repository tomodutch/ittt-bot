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
        Schema::create('schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('trigger_id')->constrained('triggers')->cascadeOnDelete();
            $table->string('type_code', 55);
            $table->dateTimeTz('one_time_at')->nullable();
            // Dont store the timezone in the time column, just store the time in UTC
            $table->time('time')->nullable();
            $table->json('days_of_the_week')->nullable();
            $table->string('timezone', 100);
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
