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
        Schema::table('steps', function (Blueprint $table) {
            // Add key as nullable first
            $table->string('key')->nullable()->after('id');
            $table->string('nextStepKey')->nullable();
            $table->string('nextStepKeyIfFalse')->nullable();
        });

        // Copy id to key
        DB::table('steps')->get()->each(function ($step) {
            DB::table('steps')->where('id', $step->id)->update(['key' => $step->id]);
        });

        if ($this->isPostgres()) {
            // Create partial index for Postgres
            DB::statement('CREATE UNIQUE INDEX steps_trigger_id_key_unique ON steps(trigger_id, key) WHERE deleted_at IS NULL;');
        } else {
            Schema::table('steps', function (Blueprint $table) {
                $table->index(['trigger_id', 'key'], 'steps_trigger_id_key_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('steps', function (Blueprint $table) {
            if ($this->isPostgres()) {
                DB::statement('DROP INDEX IF EXISTS steps_trigger_id_key_unique');
            } else {
                $table->dropIndex(['trigger_id', 'key']);
            }

            $table->dropColumn('key');
            $table->dropColumn('nextStepKey');
            $table->dropColumn('nextStepKeyIfFalse');
        });
    }

    private function isPostgres()
    {
        $connection = config('database.default');

        return $connection === 'pgsql';
    }
};
