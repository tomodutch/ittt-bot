<?php

namespace Database\Factories;

use App\Enums\LogLevel;
use App\Models\Trigger;
use App\Models\TriggerExecution;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StepExecutionLog>
 */
class StepExecutionLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "id" => $this->faker->uuid,
            "trigger_execution_id" => TriggerExecution::factory(),
            "step_id" => Trigger::factory(),
            "level" => LogLevel::Info,
            "message" => $this->faker->sentence,
            "details" => []
        ];
    }
}
