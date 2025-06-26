<?php

namespace App\Domain\Workflow\Steps;

use App\Domain\Workflow\Steps\SendEmail\SendEmailStepHandler;
use App\Domain\Workflow\Steps\SendEmail\SendEmailStepParams;
use App\Domain\Workflow\Steps\SimpleConditional\SimpleConditionalStepHandler;
use App\Domain\Workflow\Steps\SimpleConditional\SimpleConditionalStepParams;
use App\Domain\Workflow\Steps\Weather\WeatherStepData;
use App\Domain\Workflow\Steps\Weather\WeatherStepParams;

enum StepType: string
{
    case FetchWeatherForLocation = "http.weather.location";
    case SendEmail = "notify.email.send";
    case SimpleConditional = "logic.conditional.simple";

    public function handlerClass(): string
    {
        return match ($this) {
            self::FetchWeatherForLocation => WeatherStepData::class,
            self::SendEmail => SendEmailStepHandler::class,
            self::SimpleConditional => SimpleConditionalStepHandler::class
        };
    }

    public function getParamsClass(): string
    {
        return match ($this) {
            self::FetchWeatherForLocation => WeatherStepParams::class,
            self::SendEmail => SendEmailStepParams::class,
            self::SimpleConditional => SimpleConditionalStepParams::class
        };
    }
}