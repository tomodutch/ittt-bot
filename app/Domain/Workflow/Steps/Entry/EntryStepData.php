<?php

namespace App\Domain\Workflow\Steps\Entry;

use App\Data\StepData;
use App\Domain\Workflow\Steps\StepType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class EntryStepData extends StepData
{
    #[LiteralTypeScriptType('"logic.entry"')]
    public StepType $type = StepType::Entry;

    public ?EntryStepParams $params = null;
}
