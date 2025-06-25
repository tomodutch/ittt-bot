<?php

namespace App\Domain\Workflow\Steps;

use App\Contracts\WeatherFetcherContract;
use App\Domain\Workflow\Contracts\StepHandlerContract;
use App\Domain\Workflow\StepExecutionContext;
use App\Domain\Workflow\StepResultBuilder;

final class FetchWeatherForLocationStep implements StepHandlerContract
{
    public function __construct(private WeatherFetcherContract $fetcher)
    {

    }
    public function process(StepExecutionContext $context, StepResultBuilder $builder): void
    {
        $params = FetchWeatherForLocationParams::from($context->getParams());
        $location = $params->getLocation();
        $builder->info("Fetching weather for location \"{$location}\"");
        $weather = $this->fetcher->fetchWeather($location);
        $builder->info("Fetched weather for location \"{$location}\"", [
            "temp" => $weather->current->temperatureCelsius,
        ]);

        $builder->setVariable("temperatureCelsius", $weather->current->temperatureCelsius);
        $builder->setVariable("weatherConditionCode", $weather->current->weatherCondition->conditionCode);
    }
}