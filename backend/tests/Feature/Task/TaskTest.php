<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

beforeEach(function () {
    $this->creator = User::factory()->create();
    $this->member = User::factory()->create();
    $this->nonMember = User::factory()->create();

    $this->project = Project::create(['name' => 'Task Project', 'created_by' => $this->creator->id]);
    $this->project->members()->attach($this->creator->id, ['joined_at' => now()]);
    $this->project->members()->attach($this->member->id, ['joined_at' => now()]);
});

test('project member can create a task', function () {
    $response = $this->actingAs($this->member)->postJson("/api/v1/projects/{$this->project->id}/tasks", [
        'title' => 'Build API',
        'status' => 'pending',
        'deadline' => '2026-04-30',
        'assigned_to' => $this->creator->id,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'data' => ['title' => 'Build API'],
        ]);

    $this->assertDatabaseHas('tasks', [
        'title' => 'Build API',
        'project_id' => $this->project->id,
    ]);
});

test('non-member cannot create a task', function () {
    $response = $this->actingAs($this->nonMember)->postJson("/api/v1/projects/{$this->project->id}/tasks", [
        'title' => 'Sneaky Task',
    ]);

    $response->assertStatus(403);
});

test('task creation fails without title', function () {
    $response = $this->actingAs($this->member)->postJson("/api/v1/projects/{$this->project->id}/tasks", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title']);
});

test('member can update a task', function () {
    $task = Task::create([
        'project_id' => $this->project->id,
        'title' => 'Original Title',
        'created_by' => $this->creator->id,
    ]);

    $response = $this->actingAs($this->member)->putJson("/api/v1/tasks/{$task->id}", [
        'title' => 'Updated Title',
    ]);

    $response->assertOk()
        ->assertJson(['data' => ['title' => 'Updated Title']]);
});

test('member can delete a task', function () {
    $task = Task::create([
        'project_id' => $this->project->id,
        'title' => 'Delete Me',
        'created_by' => $this->creator->id,
    ]);

    $response = $this->actingAs($this->member)->deleteJson("/api/v1/tasks/{$task->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('non-member cannot update a task', function () {
    $task = Task::create([
        'project_id' => $this->project->id,
        'title' => 'Protected Task',
        'created_by' => $this->creator->id,
    ]);

    $response = $this->actingAs($this->nonMember)->putJson("/api/v1/tasks/{$task->id}", [
        'title' => 'Hacked',
    ]);

    $response->assertStatus(403);
});

test('member can update task status', function () {
    $task = Task::create([
        'project_id' => $this->project->id,
        'title' => 'Status Task',
        'status' => 'pending',
        'created_by' => $this->creator->id,
    ]);

    $response = $this->actingAs($this->member)->patchJson("/api/v1/tasks/{$task->id}/status", [
        'status' => 'working',
    ]);

    $response->assertOk()
        ->assertJson(['data' => ['status' => 'working']]);
});

test('invalid status is rejected', function () {
    $task = Task::create([
        'project_id' => $this->project->id,
        'title' => 'Status Task',
        'created_by' => $this->creator->id,
    ]);

    $response = $this->actingAs($this->member)->patchJson("/api/v1/tasks/{$task->id}/status", [
        'status' => 'invalid',
    ]);

    $response->assertStatus(422);
});

test('member can list tasks for a project', function () {
    Task::create(['project_id' => $this->project->id, 'title' => 'Task 1', 'created_by' => $this->creator->id]);
    Task::create(['project_id' => $this->project->id, 'title' => 'Task 2', 'created_by' => $this->creator->id]);

    $response = $this->actingAs($this->member)->getJson("/api/v1/projects/{$this->project->id}/tasks");

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(2);
});
