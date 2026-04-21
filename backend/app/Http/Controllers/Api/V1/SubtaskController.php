<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Subtask\Services\SubtaskService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subtask\StoreSubtaskRequest;
use App\Http\Requests\Subtask\UpdateSubtaskRequest;
use App\Http\Requests\Subtask\UpdateSubtaskStatusRequest;
use App\Http\Resources\SubtaskResource;
use App\Models\Subtask;
use App\Models\Task;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class SubtaskController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly SubtaskService $subtaskService,
    ) {}

    public function index(Task $task): JsonResponse
    {
        Gate::authorize('view', $task);

        $subtasks = $this->subtaskService->listByTask($task->id);

        return $this->success(
            SubtaskResource::collection($subtasks),
            'Subtasks retrieved.',
        );
    }

    public function store(StoreSubtaskRequest $request, Task $task): JsonResponse
    {
        Gate::authorize('update', $task);

        $subtask = $this->subtaskService->create($request->validated(), $task->id);

        return $this->created(
            new SubtaskResource($subtask->load('assignee:id,name,avatar')),
            'Subtask created successfully.',
        );
    }

    public function update(UpdateSubtaskRequest $request, Subtask $subtask): JsonResponse
    {
        Gate::authorize('update', $subtask);

        $subtask = $this->subtaskService->update($subtask, $request->validated());

        return $this->success(
            new SubtaskResource($subtask->load('assignee:id,name,avatar')),
            'Subtask updated.',
        );
    }

    public function destroy(Subtask $subtask): JsonResponse
    {
        Gate::authorize('delete', $subtask);

        $this->subtaskService->delete($subtask);

        return $this->success(null, 'Subtask deleted.');
    }

    public function updateStatus(UpdateSubtaskStatusRequest $request, Subtask $subtask): JsonResponse
    {
        Gate::authorize('updateStatus', $subtask);

        $subtask = $this->subtaskService->updateStatus($subtask, $request->validated('status'));

        return $this->success(
            new SubtaskResource($subtask->load('assignee:id,name,avatar')),
            'Subtask status updated.',
        );
    }
}
