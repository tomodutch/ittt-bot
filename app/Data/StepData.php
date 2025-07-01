<?php

namespace App\Data;

use App\Domain\Workflow\Steps\StepType;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

#[TypeScriptType('App\Domain\Workflow\Steps\SendEmail\SendEmailStepData | App\Domain\Workflow\Steps\SimpleConditional\SimpleConditionalStepData | App\Domain\Workflow\Steps\Weather\WeatherStepData | App\Domain\Workflow\Steps\Entry\EntryStepData')]
class StepData extends Data
{
    public ?string $id;
    public string $key;

    public ?string $triggerId;

    public int $order;

    public string $description;

    public StepType $type;

    public ?CarbonImmutable $createdAt;

    public ?CarbonImmutable $updatedAt;
}
