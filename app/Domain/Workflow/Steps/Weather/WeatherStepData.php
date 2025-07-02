<?php

namespace App\Domain\Workflow\Steps\Weather;

use App\Data\StepData;
use App\Domain\Workflow\Steps\StepType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

class WeatherStepData extends StepData
{
    #[LiteralTypeScriptType('"http.weather.location"')]
    public StepType $type = StepType::FetchWeatherForLocation;

    public WeatherStepParams $params;
}
