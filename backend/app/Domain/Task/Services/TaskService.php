<?php

namespace App\Domain\Task\Services;

use App\Domain\Task\Repositories\TaskRepository;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    public function __construct(
        private readonly TaskRepository $repository,
    ) {}

    public function listByProject(int $projectId, array $filters = []): Collection
    {
        return $this->repository->getByProject($projectId, $filters);
    }

    public function show(int $taskId): Task
    {
        return $this->repository->findWithSubtasks($taskId);
    }

    public function create(array $data, int $projectId, int $userId): Task
    {
        return $this->repository->create([
            'project_id' => $projectId,
            'title' => $data['title'],
            'status' => $data['status'] ?? 'pending',
            'deadline' => $data['deadline'] ?? null,
            'assigned_to' => $data['assigned_to'] ?? null,
            'created_by' => $userId,
        ]);
    }

    public function update(Task $task, array $data): Task
    {
        return $this->repository->update($task, $data);
    }

    public function updateStatus(Task $task, string $status): Task
    {
        return $this->repository->update($task, ['status' => $status]);
    }

    public function delete(Task $task): void
    {
        $this->repository->delete($task);
    }
}
