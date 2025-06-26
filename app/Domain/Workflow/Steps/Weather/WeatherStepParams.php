<?php

namespace App\Domain\Workflow\Steps\Weather;

use Spatie\LaravelData\Data;

class WeatherStepParams extends Data
{
    public function __construct(
        public readonly string $location,
    ) {
    }
}