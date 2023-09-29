<?php

namespace Database\Factories;

use App\Enums\TaskStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->word(3),
            'description' => fake()->text(500),
            'priority' => fake()->numberBetween(1, 5),
            'status' => TaskStatusEnum::Todo->value,
            'user_id' => User::factory(),
            'parent_id' =>  null
        ];
    }
}
