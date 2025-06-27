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
        public ?string $oneTimeAt,
        public ?string $time,
        public ?array $daysOfTheWeek,
        public ?string $timezone,
        public ?CarbonImmutable $createdAt,
        public ?CarbonImmutable $updatedAt,
    ) {}
}
