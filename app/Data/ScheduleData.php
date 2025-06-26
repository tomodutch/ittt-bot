<?php

namespace App\Data;

use App\Enums\ScheduleType;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class ScheduleData extends Data
{
    public function __construct(
        public ?string $id,
        public ?string $triggerId,
        public ScheduleType $typeCode,
        public ?string $oneTimeAt = null,
        public ?string $time = null,
        public ?array $daysOfTheWeek = null,
        public ?string $timezone = null,
        public ?CarbonImmutable $createdAt,
        public ?CarbonImmutable $updatedAt,
    ) {}
}
