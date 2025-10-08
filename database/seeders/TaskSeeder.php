<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    public function run()
    {
        Task::truncate(); // clear existing tasks

        // Generate 5 random tasks
        Task::factory()->count(5)->create();

        // Add specific tasks
        Task::create([
            'task_name'=>'Finish assignment',
            'description'=>'Complete the Laravel exam task.',
            'due_date'=>Carbon::now()->addHours(20),
            'priority'=>'high',
        ]);

        Task::create([
            'task_name'=>'Grocery shopping',
            'description'=>'Buy vegetables and milk.',
            'due_date'=>Carbon::now()->addDays(2),
            'priority'=>'low',
        ]);
    }
}
