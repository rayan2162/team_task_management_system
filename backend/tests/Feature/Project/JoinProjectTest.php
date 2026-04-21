<?php

use App\Models\Project;
use App\Models\User;

test('user can join a project by code', function () {
    $creator = User::factory()->create();
    $joiner = User::factory()->create();

    $project = Project::create(['name' => 'Join Test', 'created_by' => $creator->id]);
    $project->members()->attach($creator->id, ['joined_at' => now()]);

    $response = $this->actingAs($joiner)->postJson('/api/v1/projects/join', [
        'code' => $project->code,
    ]);

    $response->assertOk()
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('project_members', [
        'project_id' => $project->id,
        'user_id' => $joiner->id,
    ]);
});

test('duplicate join is blocked', function () {
    $creator = User::factory()->create();
    $project = Project::create(['name' => 'Dup Test', 'created_by' => $creator->id]);
    $project->members()->attach($creator->id, ['joined_at' => now()]);

    $response = $this->actingAs($creator)->postJson('/api/v1/projects/join', [
        'code' => $project->code,
    ]);

    $response->assertStatus(422)
        ->assertJson(['success' => false]);
});

test('join fails with invalid code', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/v1/projects/join', [
        'code' => 'INVALID_CODE',
    ]);

    $response->assertStatus(422);
});

test('join fails without code', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/v1/projects/join', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['code']);
});

test('only members can view a project', function () {
    $creator = User::factory()->create();
    $nonMember = User::factory()->create();

    $project = Project::create(['name' => 'Secret', 'created_by' => $creator->id]);
    $project->members()->attach($creator->id, ['joined_at' => now()]);

    $this->actingAs($creator)->getJson("/api/v1/projects/{$project->id}")
        ->assertOk();

    $this->actingAs($nonMember)->getJson("/api/v1/projects/{$project->id}")
        ->assertStatus(403);
});
