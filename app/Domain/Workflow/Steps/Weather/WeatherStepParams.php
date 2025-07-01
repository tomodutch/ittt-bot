<?php

namespace App\Domain\Workflow\Steps\Weather;

use App\Data\StepDataParams;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class WeatherStepParams extends StepDataParams
{
    public function __construct(
        public readonly string $location,
        public readonly string $nextStep
    ) {}

    public static function rules(?ValidationContext $context = null): array
    {
        return [
            'location' => ['required', 'string', 'max:255'],
            "nextStep" => ["required", "string"]
        ];
    }
}
