<?php

namespace App\Domain\Workflow\Contracts;

use App\Domain\Workflow\StepExecutionContext;
use App\Domain\Workflow\StepResultBuilder;

interface StepHandlerContract
{
    public function process(StepExecutionContext $context, StepResultBuilder $builder): void;
}