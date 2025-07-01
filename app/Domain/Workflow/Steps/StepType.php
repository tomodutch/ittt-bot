<?php

namespace App\Domain\Workflow\Steps;

use App\Domain\Workflow\Steps\SendEmail\SendEmailStepData;
use App\Domain\Workflow\Steps\SendEmail\SendEmailStepHandler;
use App\Domain\Workflow\Steps\SendEmail\SendEmailStepParams;
use App\Domain\Workflow\Steps\SimpleConditional\SimpleConditionalStepData;
use App\Domain\Workflow\Steps\SimpleConditional\SimpleConditionalStepHandler;
use App\Domain\Workflow\Steps\SimpleConditional\SimpleConditionalStepParams;
use App\Domain\Workflow\Steps\Weather\WeatherStepData;
use App\Domain\Workflow\Steps\Weather\WeatherStepHandler;
use App\Domain\Workflow\Steps\Weather\WeatherStepParams;

enum StepType: string
{
    case Entry = "logic.entry";
    case FetchWeatherForLocation = 'http.weather.location';
    case SendEmail = 'notify.email.send';
    case SimpleConditional = 'logic.conditional.simple';

    public function getDataClass(): string
    {
        return $this->getConfig()->dataClass;
    }

    public function getHandlerClass(): string
    {
        return $this->getConfig()->handlerClass;

    }

    public function getParamsClass(): string
    {
        return $this->getConfig()->paramsClass;
    }

    private function getConfig(): StepConfig
    {
        return match ($this) {
            self::FetchWeatherForLocation => new StepConfig(
                WeatherStepData::class,
                WeatherStepParams::class,
                WeatherStepHandler::class
            ),
            self::SendEmail => new StepConfig(
                SendEmailStepData::class,
                SendEmailStepParams::class,
                SendEmailStepHandler::class
            ),
            self::SimpleConditional => new StepConfig(
                SimpleConditionalStepData::class,
                SimpleConditionalStepParams::class,
                SimpleConditionalStepHandler::class
            )
        };
    }
}

class StepConfig
{
    public function __construct(
        public readonly string $dataClass,
        public readonly string $paramsClass,
        public readonly string $handlerClass,
    ) {}
}
