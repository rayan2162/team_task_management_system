<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Task\Services\TaskService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Requests\Task\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly TaskService $taskService,
    ) {}

    public function index(Request $request, Project $project): JsonResponse
    {
        Gate::authorize('view', $project);

        $tasks = $this->taskService->listByProject($project->id, $request->only(['status', 'assigned_to']));

        return $this->success(
            TaskResource::collection($tasks),
            'Tasks retrieved.',
        );
    }

    public function store(StoreTaskRequest $request, Project $project): JsonResponse
    {
        Gate::authorize('createTask', $project);

        $task = $this->taskService->create(
            $request->validated(),
            $project->id,
            $request->user()->id,
        );

        return $this->created(
            new TaskResource($task->load(['assignee:id,name,avatar', 'creator:id,name'])),
            'Task created successfully.',
        );
    }

    public function show(Task $task): JsonResponse
    {
        Gate::authorize('view', $task);

        $task = $this->taskService->show($task->id);

        return $this->success(
            new TaskResource($task),
            'Task retrieved.',
        );
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        Gate::authorize('update', $task);

        $task = $this->taskService->update($task, $request->validated());

        return $this->success(
            new TaskResource($task->load(['assignee:id,name,avatar', 'creator:id,name', 'subtasks.assignee:id,name,avatar'])),
            'Task updated.',
        );
    }

    public function destroy(Task $task): JsonResponse
    {
        Gate::authorize('delete', $task);

        $this->taskService->delete($task);

        return $this->success(null, 'Task deleted.');
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): JsonResponse
    {
        Gate::authorize('update', $task);

        $task = $this->taskService->updateStatus($task, $request->validated('status'));

        return $this->success(
            new TaskResource($task->load(['assignee:id,name,avatar', 'creator:id,name'])),
            'Task status updated.',
        );
    }
}
