<?php

namespace App\Domain\Workflow\Contracts;

use App\Domain\Workflow\StepExecutionContext;
use App\Domain\Workflow\StepResult;
use App\Models\Step;

interface StepProcessorContract
{
    public function process(Step $step, StepExecutionContext $context): StepResult;

}