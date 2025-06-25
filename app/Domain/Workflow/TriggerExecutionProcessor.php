<?php

namespace App\Domain\Workflow;

use App\Domain\Workflow\Contracts\StepProcessorContract;
use App\Domain\Workflow\Directive\AbortDirective;
use App\Domain\Workflow\Directive\ContinueDirective;
use App\Domain\Workflow\Directive\GoToDirective;
use App\Domain\Workflow\Directive\RetryDirective;
use App\Domain\Workflow\Directive\SkipDirective;
use App\Enums\ExecutionStatus;
use App\Models\TriggerExecution;

class TriggerExecutionProcessor
{
    public function __construct(private StepProcessorContract $stepProcessor)
    {

    }

    public function process(TriggerExecution $triggerExecution)
    {
        $triggerExecution->status_code = ExecutionStatus::Running;
        $triggerExecution->save();

        $context = new StepExecutionContext([]);
        $steps = $triggerExecution->trigger->steps()->orderBy("order")->get();
        $currentIndex = 0;
        $doAbort = false;
        $maxLoops = 100;
        while ($currentIndex < $maxLoops && !$doAbort && isset($steps[$currentIndex])) {
            $step = $steps[$currentIndex];
            $result = $this->stepProcessor->process($step, $context);
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