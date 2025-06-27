<?php

namespace App\Jobs;

use App\Domain\Workflow\TriggerExecutionProcessor;
use App\Models\TriggerExecution;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RunTriggerExecution implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private string $id)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(TriggerExecutionProcessor $processor): void
    {
        $execution = TriggerExecution::find($this->id);
        if (!$execution) {
            Log::info("Trigger execution with ID {$this->id} not found.");
            return;
        }

        $processor->process($execution);
    }
}
