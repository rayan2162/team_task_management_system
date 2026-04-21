<?php

use App\Models\Project;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;

beforeEach(function () {
    $this->creator = User::factory()->create();
    $this->assignee = User::factory()->create();
    $this->nonMember = User::factory()->create();

    $this->project = Project::create(['name' => 'Subtask Project', 'created_by' => $this->creator->id]);
    $this->project->members()->attach($this->creator->id, ['joined_at' => now()]);
    $this->project->members()->attach($this->assignee->id, ['joined_at' => now()]);

    $this->task = Task::create([
        'project_id' => $this->project->id,
        'title' => 'Parent Task',
        'created_by' => $this->creator->id,
    ]);
});

test('member can create a subtask', function () {
    $response = $this->actingAs($this->creator)->postJson("/api/v1/tasks/{$this->task->id}/subtasks", [
        'body' => 'Implement auth middleware',
        'status' => 'pending',
        'deadline' => '2026-04-25',
        'assigned_to' => $this->assignee->id,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'data' => ['body' => 'Implement auth middleware'],
        ]);
});

test('subtask creation fails without body', function () {
    $response = $this->actingAs($this->creator)->postJson("/api/v1/tasks/{$this->task->id}/subtasks", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['body']);
});

test('non-member cannot create subtask', function () {
    $response = $this->actingAs($this->nonMember)->postJson("/api/v1/tasks/{$this->task->id}/subtasks", [
        'body' => 'Sneaky subtask',
    ]);

    $response->assertStatus(403);
});

test('assignee can update subtask status', function () {
    $subtask = Subtask::create([
        'task_id' => $this->task->id,
        'body' => 'Test subtask',
        'assigned_to' => $this->assignee->id,
    ]);

    $response = $this->actingAs($this->assignee)->patchJson("/api/v1/subtasks/{$subtask->id}/status", [
        'status' => 'working',
    ]);

    $response->assertOk()
        ->assertJson(['data' => ['status' => 'working']]);
});

test('project creator can update subtask status', function () {
    $subtask = Subtask::create([
        'task_id' => $this->task->id,
        'body' => 'Test subtask',
        'assigned_to' => $this->assignee->id,
    ]);

    $response = $this->actingAs($this->creator)->patchJson("/api/v1/subtasks/{$subtask->id}/status", [
        'status' => 'done',
    ]);

    $response->assertOk()
        ->assertJson(['data' => ['status' => 'done']]);
});

test('non-assignee non-creator cannot update subtask status', function () {
    $otherMember = User::factory()->create();
    $this->project->members()->attach($otherMember->id, ['joined_at' => now()]);

    $subtask = Subtask::create([
        'task_id' => $this->task->id,
        'body' => 'Protected subtask',
        'assigned_to' => $this->assignee->id,
    ]);

    $response = $this->actingAs($otherMember)->patchJson("/api/v1/subtasks/{$subtask->id}/status", [
        'status' => 'done',
    ]);

    $response->assertStatus(403);
});

test('member can delete a subtask', function () {
    $subtask = Subtask::create([
        'task_id' => $this->task->id,
        'body' => 'Delete me',
    ]);

    $response = $this->actingAs($this->creator)->deleteJson("/api/v1/subtasks/{$subtask->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('subtasks', ['id' => $subtask->id]);
});

test('member can list subtasks for a task', function () {
    Subtask::create(['task_id' => $this->task->id, 'body' => 'Sub 1']);
    Subtask::create(['task_id' => $this->task->id, 'body' => 'Sub 2']);

    $response = $this->actingAs($this->creator)->getJson("/api/v1/tasks/{$this->task->id}/subtasks");

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(2);
});
