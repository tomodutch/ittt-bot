<?php

namespace Database\Factories;

use App\Enums\ScheduleType;
use App\Models\Trigger;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'trigger_id' => Trigger::factory(),
            'type_code' => ScheduleType::Daily,
            'one_time_at' => null,
            'time' => $this->faker->time('H:i:s'),
            'days_of_the_week' => null,
            'timezone' => 'UTC',
        ];
    }

    public function once(): static
    {
        return $this->state(fn () => [
            'type_code' => ScheduleType::Once,
            'one_time_at' => $this->faker->dateTimeBetween('+1 hour', '+3 days'),
            'time' => null,
            'days_of_the_week' => null,
        ]);
    }

    public function daily(): static
    {
        return $this->state(fn () => [
            'type_code' => ScheduleType::Daily,
            'one_time_at' => null,
            'time' => $this->faker->time('H:i:s'),
            'days_of_the_week' => [0, 1, 2, 3, 4, 5, 6],
        ]);
    }

    public function weekly(): static
    {
        return $this->state(fn () => [
            'type_code' => ScheduleType::Weekly,
            'one_time_at' => null,
            'time' => $this->faker->time('H:i:s'),
            'days_of_the_week' => $this->faker->randomElements(range(1, 7), rand(1, 3)),
        ]);
    }
}
