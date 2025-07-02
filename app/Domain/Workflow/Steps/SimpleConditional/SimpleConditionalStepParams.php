<?php

namespace App\Domain\Workflow\Steps\SimpleConditional;

use App\Data\StepDataParams;
use App\Enums\Operator;

final class SimpleConditionalStepParams extends StepDataParams
{
    public string $left;

    public Operator $operator;

    public mixed $right;
}
