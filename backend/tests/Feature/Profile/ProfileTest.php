<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('user can get their profile', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/profile');

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
});

test('user can update their profile', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patchJson('/api/v1/profile', [
        'name' => 'Updated Name',
    ]);

    $response->assertOk()
        ->assertJson(['data' => ['name' => 'Updated Name']]);
});

test('user can upload avatar', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

    $response = $this->actingAs($user)->postJson('/api/v1/profile/avatar', [
        'avatar' => $file,
    ]);

    $response->assertOk()
        ->assertJson(['success' => true]);

    $user->refresh();
    expect($user->avatar)->not()->toBeNull();
    Storage::disk('public')->assertExists($user->avatar);
});

test('avatar upload rejects non-image', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 1024);

    $response = $this->actingAs($user)->postJson('/api/v1/profile/avatar', [
        'avatar' => $file,
    ]);

    $response->assertStatus(422);
});
