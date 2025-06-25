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
        Schema::create('steps', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("trigger_id")->constrained("triggers")->cascadeOnDelete();
            $table->string("type", 55);
            $table->string("description", 150);
            $table->integer("order");
            $table->json("params")->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index(['trigger_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('steps');
    }
};
