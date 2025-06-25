<?php

namespace Database\Factories;

use App\Enums\ExecutionStatus;
use App\Enums\ExecutionType;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trigger>
 */
class TriggerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "user_id" => User::factory(),
            "name" => $this->faker->name(),
            "description" => $this->faker->realTextBetween(10, 150),
            "execution_type" => ExecutionType::Schedule,
        ];
    }

    public function webhook()
    {
        return $this->state(fn() => [
            'execution_type' => ExecutionType::Webhook,
        ]);
    }

    public function scheduled()
    {
        return $this->state(fn() => [
            'execution_type' => ExecutionType::Schedule,
        ]);
    }
}
