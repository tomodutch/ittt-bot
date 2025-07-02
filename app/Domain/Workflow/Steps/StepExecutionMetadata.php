<?php

namespace App\Domain\Workflow\Steps;

class StepExecutionMetadata
{
    public function __construct(public readonly ?string $nextStepKey, public readonly ?string $nextStepKeyIfFalse) {}
}
