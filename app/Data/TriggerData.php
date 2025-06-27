<?php

namespace App\Data;

use App\Enums\ExecutionType;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class TriggerData extends Data
{
    public function __construct(
        public ?string $id,
        public string $name,
        public string $description,
        public ExecutionType $executionType,
        #[DataCollectionOf(ScheduleData::class)]
        public ?Collection $schedules,
        #[DataCollectionOf(TriggerExecutionData::class)]
        public ?Collection $executions = new Collection(),
        #[DataCollectionOf(StepData::class)]
        #[WithCast(StepDataPolymorphicCaster::class)]
        public ?Collection $steps,
        public ?CarbonImmutable $createdAt,
        public ?CarbonImmutable $updatedAt,
    ) {
    }
}