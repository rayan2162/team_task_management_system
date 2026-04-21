<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Profile\Services\ProfileService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ProfileService $profileService,
    ) {}

    public function show(Request $request): JsonResponse
    {
        return $this->success(
            new UserResource($request->user()),
            'Profile retrieved.',
        );
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->profileService->update(
            $request->user(),
            $request->validated(),
        );

        return $this->success(
            new UserResource($user),
            'Profile updated.',
        );
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $user = $this->profileService->uploadAvatar(
            $request->user(),
            $request->file('avatar'),
        );

        return $this->success(
            new UserResource($user),
            'Avatar uploaded successfully.',
        );
    }
}
