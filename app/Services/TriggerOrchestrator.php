<?php

namespace App\Services;

use App\Enums\ExecutionStatus;
use App\Enums\RunReason;
use App\Models\Schedule;
use App\Models\TriggerExecution;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TriggerOrchestrator
{
    /**
     * Summary of process
     * @param \Illuminate\Support\Collection<int, \App\Models\Schedule> $schedules
     * @return void
     */
    public function process(Collection $schedules)
    {
        $now = now("UTC");

        Log::info("Start processing schedules", [
            "schedulesCount" => $schedules->count()
        ]);

        $triggerGroups = $schedules->groupBy(fn(Schedule $schedule) => $schedule->trigger_id);

        Log::debug("Map grouped schedules into TriggerExecution data and keep schedules for pivot");
        $triggerExecutionsData = $triggerGroups->map(function ($schedules, $triggerId) use ($now) {
            $model = new TriggerExecution([
                'trigger_id' => $triggerId,
                'origin_type' => 'CRON',
                'origin_id' => '',
                'status_code' => ExecutionStatus::Idle,
                'run_reason_code' => RunReason::Scheduled,
                'context' => [],
            ]);

            return [
                'id' => (string) Str::uuid(),
                'trigger_id' => $model->trigger_id,
                'origin_type' => $model->origin_type,
                'origin_id' => $model->origin_id,
                'status_code' => $model->status_code->value,
                'run_reason_code' => $model->run_reason_code->value,
                'context' => json_encode($model->context),
                'created_at' => $now,
                'updated_at' => $now,
                '_schedule_ids' => $schedules->pluck('id'),  // keep schedule IDs for pivot
            ];
        });

        Log::debug("Bulk insert TriggerExecutions (exclude _schedule_ids)");
        $insertData = $triggerExecutionsData->map(fn($data) => collect($data)->except('_schedule_ids')->toArray());
        TriggerExecution::insert($insertData->toArray());

        Log::debug("Prepare pivot table data linking trigger_executions to schedules");
        $pivotData = $triggerExecutionsData->flatMap(function ($data) {
            $triggerExecutionId = $data['id'];
            return $data['_schedule_ids']->map(fn($scheduleId) => [
                'trigger_execution_id' => $triggerExecutionId,
                'schedule_id' => $scheduleId,
            ]);
        });

        Log::debug("Insert into pivot table");
        DB::table('schedule_trigger_execution')->insert($pivotData->toArray());
    }
}