<?php

namespace Database\Factories;

use App\Models\Step;

class WorkflowFactory
{
    public static function createWeatherWorkflow()
    {
        return [
            Step::factory()->entry()->create(['nextStepKey' => 'weather']),
            Step::factory()->fetchWeatherAction()->create(['nextStepKey' => 'conditional']),
            Step::factory()->conditional()->create([
                'nextStepKey' => 'sendEmail',
                'params' => [
                    'left' => 'weather.temperature',
                    'operator' => '>=',
                    'right' => 10,
                ],
            ]),
            Step::factory()->sendEmail()->create([]),
        ];
    }
}
