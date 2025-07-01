<?php

namespace App\Domain\Workflow\Steps\Entry;

use App\Data\StepDataParams;

final class EntryStepParams extends StepDataParams
{
    public function __construct(
        public readonly string $nextStep
    ) {}
}
