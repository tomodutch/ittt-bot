<?php

namespace Database\Factories;

use App\Enums\ExecutionStatus;
use App\Enums\RunReason;
use App\Models\Trigger;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TriggerExecution>
 */
class TriggerExecutionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trigger_id' => Trigger::factory(),
            'origin_type' => 'SYSTEM',
            'origin_id' => $this->faker->uuid(),
            'status_code' => ExecutionStatus::Idle,
            'run_reason_code' => RunReason::Scheduled,
            'context' => [],
        ];
    }

    public function idle(): TriggerExecutionFactory
    {
        return $this->state(fn () => [
            'status_code' => ExecutionStatus::Idle,
        ]);
    }
}
