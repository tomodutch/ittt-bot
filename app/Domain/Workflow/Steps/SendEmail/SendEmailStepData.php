<?php

namespace App\Domain\Workflow\Steps\SendEmail;

use App\Data\StepData;
use App\Domain\Workflow\Steps\StepType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class SendEmailStepData extends StepData
{
    #[LiteralTypeScriptType('"notify.email.send"')]
    public StepType $type = StepType::SendEmail;

    public SendEmailStepParams $params;
}
