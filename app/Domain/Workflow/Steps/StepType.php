<?php

namespace App\Domain\Workflow\Steps;

enum StepType: string
{
    case FetchWeatherForLocation = "http.weather.location";
    case SendEmail = "notify.email.send";
    case SimpleConditional = "logic.conditional.simple";

    public function handlerClass(): string
    {
        return match ($this) {
            self::FetchWeatherForLocation => FetchWeatherForLocationStep::class,
            self::SendEmail => SendEmailStep::class,
            self::SimpleConditional => SimpleConditionalStep::class
        };
    }

    public function getParamsClass(): string
    {
        return match ($this) {
            self::FetchWeatherForLocation => FetchWeatherForLocationParams::class,
            self::SendEmail => SendEmailParams::class,
            self::SimpleConditional => SimpleConditionalParams::class
        };
    }
}