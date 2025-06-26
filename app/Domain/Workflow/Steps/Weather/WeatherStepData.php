<?php

namespace App\Domain\Workflow\Steps\Weather;

use App\Domain\Workflow\Steps\StepType;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

class WeatherStepData extends Data
{
    #[LiteralTypeScriptType('"http.weather.location"')]
    public string $type = StepType::FetchWeatherForLocation->value;
    public function __construct(
        public readonly WeatherStepParams $params,
    ) {

    }
}

