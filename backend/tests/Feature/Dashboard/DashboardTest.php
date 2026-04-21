<?php

use App\Models\Project;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;

test('dashboard returns correct stats', function () {
    $user = User::factory()->create();

    $project = Project::create(['name' => 'Dashboard Test', 'created_by' => $user->id]);
    $project->members()->attach($user->id, ['joined_at' => now()]);

    $task1 = Task::create(['project_id' => $project->id, 'title' => 'T1', 'status' => 'done', 'assigned_to' => $user->id, 'created_by' => $user->id]);
    $task2 = Task::create(['project_id' => $project->id, 'title' => 'T2', 'status' => 'working', 'assigned_to' => $user->id, 'created_by' => $user->id]);
    Task::create(['project_id' => $project->id, 'title' => 'T3', 'status' => 'pending', 'created_by' => $user->id]);

    Subtask::create(['task_id' => $task1->id, 'body' => 'S1', 'assigned_to' => $user->id]);
    Subtask::create(['task_id' => $task2->id, 'body' => 'S2', 'assigned_to' => $user->id]);

    $response = $this->actingAs($user)->getJson('/api/v1/dashboard');

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'data' => [
                'assigned_tasks' => 2,
                'assigned_subtasks' => 2,
                'completed_tasks' => 1,
                'completion_rate' => 50,
            ],
        ]);
});

test('dashboard analytics returns data', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/dashboard/analytics');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('unauthenticated user cannot access dashboard', function () {
    $response = $this->getJson('/api/v1/dashboard');

    $response->assertStatus(401);
});
