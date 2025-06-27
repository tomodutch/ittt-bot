<?php

namespace App\Domain\Workflow;

use App\Data\StepExecutionLogData;
use App\Domain\Workflow\Contracts\StepProcessorContract;
use App\Domain\Workflow\Directive\AbortDirective;
use App\Domain\Workflow\Directive\ContinueDirective;
use App\Domain\Workflow\Directive\GoToDirective;
use App\Domain\Workflow\Directive\RetryDirective;
use App\Domain\Workflow\Directive\SkipDirective;
use App\Enums\ExecutionStatus;
use App\Models\StepExecutionLog;
use App\Models\TriggerExecution;
use Illuminate\Support\Collection;

class TriggerExecutionProcessor
{
    public function __construct(private StepProcessorContract $stepProcessor)
    {

    }

    public function process(TriggerExecution $triggerExecution)
    {
        $triggerExecution->status_code = ExecutionStatus::Running;
        $triggerExecution->save();

        $context = new StepExecutionContext(new Collection());
        $steps = $triggerExecution->trigger->steps()->orderBy("order")->get();
        $currentIndex = 0;
        $doAbort = false;
        $maxLoops = 100;
        while ($currentIndex < $maxLoops && !$doAbort && isset($steps[$currentIndex])) {
            $step = $steps[$currentIndex];
            $result = $this->stepProcessor->process($step, $context);
            $logs = $result->getLogs();

            $triggerExecution->logs()->saveMany(
                $logs->map(function (array $log) use ($triggerExecution, $step) {
                    return new StepExecutionLog([
                        'trigger_execution_id' => $triggerExecution->id,
                        'step_id' => $step->id,
                        'level' => $log['level'],
                        'message' => $log['message'],
                        'details' => $log['context'] ?? []
                    ]);
                })
            );
            $context = $context->merge($result->getVariables());

            switch (get_class($result->getDirective())) {
                case ContinueDirective::class:
                    $currentIndex++;
                    break;

                case AbortDirective::class:
                    $doAbort = true;
                    break;

                case RetryDirective::class:
                    break;

                case SkipDirective::class:
                    $currentIndex++;
                    break;

                case GoToDirective::class:
                    break;

                default:
                    throw new \LogicException("Unknown flow directive");
            }
        }

        $triggerExecution->status_code = ExecutionStatus::Finished;
        $triggerExecution->save();
    }
}