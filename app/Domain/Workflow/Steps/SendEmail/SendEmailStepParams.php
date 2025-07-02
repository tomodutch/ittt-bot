<?php

namespace App\Domain\Workflow\Steps\SendEmail;

use App\Data\StepDataParams;

final class SendEmailStepParams extends StepDataParams
{
    public string $to;

    public ?string $cc;

    public ?string $bcc;

    public string $subject;

    public string $body;
}
