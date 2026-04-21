<?php

namespace App\Domain\Subtask\Services;

use App\Domain\Subtask\Repositories\SubtaskRepository;
use App\Models\Subtask;
use Illuminate\Database\Eloquent\Collection;

class SubtaskService
{
    public function __construct(
        private readonly SubtaskRepository $repository,
    ) {}

    public function listByTask(int $taskId): Collection
    {
        return $this->repository->getByTask($taskId);
    }

    public function create(array $data, int $taskId): Subtask
    {
        return $this->repository->create([
            'task_id' => $taskId,
            'body' => $data['body'],
            'status' => $data['status'] ?? 'pending',
            'deadline' => $data['deadline'] ?? null,
            'assigned_to' => $data['assigned_to'] ?? null,
        ]);
    }

    public function update(Subtask $subtask, array $data): Subtask
    {
        return $this->repository->update($subtask, $data);
    }

    public function updateStatus(Subtask $subtask, string $status): Subtask
    {
        return $this->repository->update($subtask, ['status' => $status]);
    }

    public function delete(Subtask $subtask): void
    {
        $this->repository->delete($subtask);
    }
}
