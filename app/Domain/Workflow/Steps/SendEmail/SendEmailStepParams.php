<?php

namespace App\Domain\Workflow\Steps\SendEmail;

use Spatie\LaravelData\Data;

final class SendEmailStepParams extends Data
{
    public function __construct(
        public readonly string $to,
        public readonly ?string $cc,
        public readonly ?string $bcc,
        public readonly string $subject,
        public readonly string $body
    ) {
    }
}