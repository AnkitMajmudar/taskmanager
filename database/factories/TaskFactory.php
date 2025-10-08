<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Task;
use Carbon\Carbon;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'task_name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'due_date' => Carbon::now()->addDays(rand(1, 5)),
            'priority' => $this->faker->randomElement(['low','medium','high']),
        ];
    }
}
