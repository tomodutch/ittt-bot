<?php

namespace App\Domain\Workflow;

use App\Domain\Workflow\Contracts\StepProcessorContract;
use App\Domain\Workflow\Directive\AbortDirective;
use App\Domain\Workflow\Directive\ContinueDirective;
use App\Domain\Workflow\Steps\StepType;
use App\Enums\ExecutionStatus;
use App\Models\StepExecutionLog;
use App\Models\TriggerExecution;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class TriggerExecutionProcessor
{
    public function __construct(private StepProcessorContract $stepProcessor) {}

    public function process(TriggerExecution $triggerExecution)
    {
        $triggerExecution->status_code = ExecutionStatus::Running;
        $triggerExecution->save();

        $steps = $triggerExecution->trigger->steps()->get();
        $entryStep = $steps->first(function ($step) {
            return $step->type->value === StepType::Entry->value;
        });

        if (! $entryStep) {
            Log::info("Abort trigger execution {$triggerExecution->id} because no entry step was found");
            $triggerExecution->status_code = ExecutionStatus::Finished;
            $triggerExecution->save();

            return;
        }

        $stepsMap = $steps->keyBy(function ($step) {
            return $step->key;
        });
        $context = new StepExecutionContext(new Collection);

        $step = $entryStep;
        $currentIndex = 0;
        $doAbort = false;
        $maxLoops = 100;
        while ($step && $currentIndex < $maxLoops && ! $doAbort) {
            $result = $this->stepProcessor->process($step, $context);
            $logs = $result->getLogs();

            $triggerExecution->logs()->saveMany(
                $logs->map(function (array $log) use ($triggerExecution, $step) {
                    return new StepExecutionLog([
                        'trigger_execution_id' => $triggerExecution->id,
                        'step_id' => $step->id,
                        'level' => $log['level'],
                        'message' => $log['message'],
                        'details' => $log['context'] ?? [],
                    ]);
                })
            );
            $context = $context->merge($result->getVariables());

            $directive = $result->getDirective();
            if ($directive instanceof ContinueDirective) {
                $nextStepKey = $directive->nextStepKey;
                if ($nextStepKey && $nextStep = $stepsMap->get($nextStepKey)) {
                    $step = $nextStep;
                } else {
                    $doAbort = true;
                }
            } elseif ($directive instanceof AbortDirective) {
                $doAbort = true;
            }
        }

        $triggerExecution->status_code = ExecutionStatus::Finished;
        $triggerExecution->save();
    }
}
