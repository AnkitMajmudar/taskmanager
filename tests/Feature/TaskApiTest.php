<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_fetch_tasks()
    {
        Task::factory()->count(3)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'task_name', 'description', 'due_date', 'priority', 'is_completed', 'due_soon']
                     ],
                     'pagination' => ['current_page','last_page','per_page','total'],
                     'success'
                 ]);
    }

    /** @test */
    public function it_can_create_a_task()
    {
        $payload = [
            'task_name' => 'New Task',
            'description' => 'Test description',
            'due_date' => now()->addDay()->format('Y-m-d H:i:s'),
            'priority' => 'medium'
        ];

        $response = $this->postJson('/api/tasks', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['task_name' => 'New Task']);
        $this->assertDatabaseHas('tasks', ['task_name' => 'New Task']);
    }

    /** @test */
    public function it_can_update_a_task()
    {
        $task = Task::factory()->create();

        $payload = [
            'task_name' => 'Updated Task',
            'description' => 'Updated description',
            'due_date' => now()->addDay()->format('Y-m-d H:i:s'),
            'priority' => 'medium'
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment(['task_name' => 'Updated Task']);

        $this->assertDatabaseHas('tasks', ['task_name' => 'Updated Task']);
    }

    /** @test */
    public function it_can_delete_a_task()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function it_can_toggle_task_status()
    {
        $task = Task::factory()->create(['is_completed' => false]);

        $response = $this->patchJson("/api/tasks/{$task->id}/status", ['is_completed' => true]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['is_completed' => true]);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'is_completed' => true]);
    }
}
