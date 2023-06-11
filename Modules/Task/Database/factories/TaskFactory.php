<?php

namespace Modules\Task\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Task\Models\Task;

class TaskFactory extends Factory
{
    protected $model = Task::class;
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'comment' => fake()->text(30),
            'date' => fake()->date(),
            'time_spent' => fake()->numberBetween(1, 200),
        ];
    }
}
