<?php

namespace App\Domain\Workflow\Steps\SimpleConditional;

use App\Data\StepDataParams;
use App\Enums\Operator;

final class SimpleConditionalStepParams extends StepDataParams
{
    public function __construct(
        public readonly string $left,
        public readonly Operator $operator,
        public readonly mixed $right,
        public readonly string $nextStepIfTrue,
        public readonly string $nextStepIfFalse
    ) {
    }
}
