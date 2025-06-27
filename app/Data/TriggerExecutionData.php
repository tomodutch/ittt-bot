<?php

namespace App\Data;

use App\Enums\ExecutionStatus;
use App\Enums\RunReason;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class TriggerExecutionData extends Data
{
    public function __construct(
        public string $id,
        public string $triggerId,
        public ?string $originType,
        public ?string $originId,
        public ExecutionStatus $statusCode,
        public RunReason $runReasonCode,
        public ?array $context = [],
        #[DataCollectionOf(StepExecutionLogData::class)]
        public Collection $logs = new Collection,
        public ?string $finishedAt = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}
}
