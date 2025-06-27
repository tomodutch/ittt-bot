<?php

namespace App\Data;

use App\Enums\LogLevel;
use Spatie\LaravelData\Data;

class StepExecutionLogData extends Data
{
    public function __construct(
        public string $id,
        public string $triggerExecutionId,
        public string $stepId,
        public LogLevel $level,
        public string $message,
        public ?array $details = null
    ) {
    }
}