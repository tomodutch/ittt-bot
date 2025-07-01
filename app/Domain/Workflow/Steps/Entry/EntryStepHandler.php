<?php

namespace App\Domain\Workflow\Steps\Entry;

use App\Domain\Workflow\Contracts\StepHandlerContract;
use App\Domain\Workflow\StepExecutionContext;
use App\Domain\Workflow\StepResultBuilder;

final class EntryStepHandler implements StepHandlerContract
{
    public function process(StepExecutionContext $context, StepResultBuilder $builder): void
    {
    }
}
