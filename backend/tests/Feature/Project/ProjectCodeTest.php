<?php

use App\Models\Project;
use App\Models\User;

test('project code starts with PRJ and has correct length', function () {
    $user = User::factory()->create();
    $project = Project::create(['name' => 'Test', 'created_by' => $user->id]);

    expect($project->code)->toStartWith('PRJ');
    expect(strlen($project->code))->toBe(9);
});

test('project code is alphanumeric', function () {
    $user = User::factory()->create();
    $project = Project::create(['name' => 'Test', 'created_by' => $user->id]);

    expect($project->code)->toMatch('/^PRJ[A-Z0-9]{6}$/');
});

test('generated project codes are unique', function () {
    $user = User::factory()->create();

    $codes = collect(range(1, 20))->map(function ($i) use ($user) {
        $project = Project::create(['name' => "Project $i", 'created_by' => $user->id]);
        return $project->code;
    });

    expect($codes->unique()->count())->toBe(20);
});
