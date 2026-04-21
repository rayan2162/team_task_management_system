<?php

use App\Models\Project;
use App\Models\User;

test('authenticated user can create a project', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/v1/projects', [
        'name' => 'My Test Project',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'data' => ['name' => 'My Test Project'],
        ]);

    $this->assertDatabaseHas('projects', ['name' => 'My Test Project']);

    $project = Project::where('name', 'My Test Project')->first();
    expect($project->code)->toStartWith('PRJ');
    expect($project->created_by)->toBe($user->id);

    $this->assertDatabaseHas('project_members', [
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);
});

test('project creation fails without name', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/v1/projects', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('unauthenticated user cannot create project', function () {
    $response = $this->postJson('/api/v1/projects', ['name' => 'Test']);

    $response->assertStatus(401);
});

test('user can list their projects', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $project = Project::create(['name' => 'My Project', 'created_by' => $user->id]);
    $project->members()->attach($user->id, ['joined_at' => now()]);

    $otherProject = Project::create(['name' => 'Other Project', 'created_by' => $otherUser->id]);
    $otherProject->members()->attach($otherUser->id, ['joined_at' => now()]);

    $response = $this->actingAs($user)->getJson('/api/v1/projects');

    $response->assertOk()
        ->assertJson(['success' => true]);

    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.name'))->toBe('My Project');
});
