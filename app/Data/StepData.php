<?php

namespace App\Data;

use App\Domain\Workflow\Steps\SendEmail\SendEmailStepParams;
use App\Domain\Workflow\Steps\SimpleConditional\SimpleConditionalStepParams;
use App\Domain\Workflow\Steps\StepType;
use App\Domain\Workflow\Steps\Weather\WeatherStepParams;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class StepData extends Data
{
    public function __construct(
        public ?string $id,
        public ?string $triggerId,
        public int $order,
        public string $description,
        public StepType $type,
        public ?string $action = null,
        public SendEmailStepParams|WeatherStepParams|SimpleConditionalStepParams $params,
        public ?CarbonImmutable $createdAt,
        public ?CarbonImmutable $updatedAt,
    ) {
    }
}
