<?php

namespace App\Domain\Workflow\Steps\SimpleConditional;

use App\Domain\Workflow\Steps\StepType;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class SimpleConditionalStepData extends Data
{
    #[LiteralTypeScriptType('"logic.conditional.simple"')]
    public string $type = StepType::SimpleConditional->value;
    public function __construct(
        public readonly SimpleConditionalStepParams $params,
    ) {
    }
}