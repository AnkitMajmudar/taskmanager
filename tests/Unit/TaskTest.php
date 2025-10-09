<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_task()
    {
        $task = Task::create([
            'task_name' => 'Test Task',
            'description' => 'This is a test task.',
            'due_date' => now()->addDay(),
            'priority' => 'high',
            'is_completed' => false,
        ]);

        $this->assertDatabaseHas('tasks', [
            'task_name' => 'Test Task',
            'description' => 'This is a test task.',
        ]);
    }

    /** @test */
    public function it_casts_is_completed_to_boolean()
    {
        $task = Task::factory()->create(['is_completed' => 1]);
        $this->assertIsBool($task->is_completed);
    }
}
