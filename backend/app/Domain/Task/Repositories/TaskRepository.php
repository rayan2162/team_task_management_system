<?php

namespace App\Domain\Task\Repositories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    public function getByProject(int $projectId, array $filters = []): Collection
    {
        $query = Task::where('project_id', $projectId)
            ->with(['assignee:id,name,avatar', 'subtasks.assignee:id,name,avatar', 'creator:id,name']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        return $query->latest()->get();
    }

    public function findWithSubtasks(int $taskId): Task
    {
        return Task::with(['subtasks.assignee:id,name,avatar', 'assignee:id,name,avatar', 'creator:id,name', 'project'])
            ->findOrFail($taskId);
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task->fresh();
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }
}
