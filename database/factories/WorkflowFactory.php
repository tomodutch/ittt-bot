<?php

namespace Database\Factories;

use App\Models\Step;

class WorkflowFactory
{
    public static function createWeatherWorkflow()
    {
        return [
            Step::factory()->fetchWeatherAction()->create(["order" => 0]),
            Step::factory()->conditional()->create(["order" => 1]),
            Step::factory()->sendEmail()->create(["order" => 2]),
        ];
    }
}