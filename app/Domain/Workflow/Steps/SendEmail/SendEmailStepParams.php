<?php

namespace App\Domain\Workflow\Steps\SendEmail;

use App\Data\StepDataParams;

final class SendEmailStepParams extends StepDataParams
{
    public function __construct(
        public readonly string $to,
        public readonly ?string $cc,
        public readonly ?string $bcc,
        public readonly string $subject,
        public readonly string $body,
        public readonly string $nextStep
    ) {}
}
