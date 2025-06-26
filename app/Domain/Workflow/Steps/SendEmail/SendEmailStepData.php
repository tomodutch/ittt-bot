<?php

namespace App\Domain\Workflow\Steps\SendEmail;

use App\Domain\Workflow\Steps\StepType;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class SendEmailStepData extends Data
{
    #[LiteralTypeScriptType('"notify.email.send"')]
    public string $type = StepType::SendEmail->value;
    public function __construct(
        public readonly SendEmailStepParams $params
    ) {
    }
}