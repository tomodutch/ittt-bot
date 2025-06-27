<?php

namespace Database\Factories;

use App\Domain\Workflow\Steps\StepType;
use App\Models\Trigger;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Step>
 */
class StepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'trigger_id' => Trigger::factory(),
            'description' => $this->faker->realTextBetween(10, 150),
            'order' => $this->faker->numberBetween(0, 10),
            'params' => [],
        ];
    }

    public function fetchWeatherAction()
    {
        return $this->state([
            'type' => StepType::FetchWeatherForLocation,
            'params' => [
                'location' => 'London',
            ],
        ]);
    }

    public function sendEmail()
    {
        return $this->state([
            'type' => StepType::SendEmail,
            'params' => [
                'to' => $this->faker->email(),
                'message' => $this->faker->text(),
            ],
        ]);
    }

    public function conditional()
    {
        return $this->state([
            'type' => StepType::SimpleConditional,
        ]);
    }
}
