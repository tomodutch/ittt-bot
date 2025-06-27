<?php

namespace App\Domain\Workflow\Steps\SimpleConditional;

use App\Data\StepData;
use App\Domain\Workflow\Steps\StepType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class SimpleConditionalStepData extends StepData
{
    #[LiteralTypeScriptType('"logic.conditional.simple"')]
    public StepType $type = StepType::SimpleConditional;

    public function __construct(
        public readonly SimpleConditionalStepParams $params,
    ) {}
}
