<?php

namespace App\Domain\Workflow\Steps\Weather;

use App\Contracts\WeatherFetcherContract;
use App\Domain\Workflow\Contracts\StepHandlerContract;
use App\Domain\Workflow\Directive\ContinueDirective;
use App\Domain\Workflow\StepExecutionContext;
use App\Domain\Workflow\StepResultBuilder;

final class WeatherStepHandler implements StepHandlerContract
{
    public function __construct(private WeatherFetcherContract $fetcher) {}

    public function process(StepExecutionContext $context, StepResultBuilder $builder): void
    {
        $params = WeatherStepParams::from($context->getParams());
        $location = $params->location;
        $builder->info("Fetching weather for location \"{$location}\"");
        $weather = $this->fetcher->fetchWeather($location);
        $builder->info("Fetched weather for location \"{$location}\"", [
            'temp' => $weather->current->temperatureCelsius,
        ]);

        $builder->setVariable('temperatureCelsius', $weather->current->temperatureCelsius);
        $builder->setVariable('weatherCondition', $weather->current->weatherCondition->text);
        $builder->setVariable('weatherConditionCode', $weather->current->weatherCondition->conditionCode);
        $builder->setDirective(new ContinueDirective($context->getNextStepKey()));
    }
}
