<?php

namespace App\Domain\Subtask\Repositories;

use App\Models\Subtask;
use Illuminate\Database\Eloquent\Collection;

class SubtaskRepository
{
    public function getByTask(int $taskId): Collection
    {
        return Subtask::where('task_id', $taskId)
            ->with('assignee:id,name,avatar')
            ->latest()
            ->get();
    }

    public function create(array $data): Subtask
    {
        return Subtask::create($data);
    }

    public function update(Subtask $subtask, array $data): Subtask
    {
        $subtask->update($data);
        return $subtask->fresh();
    }

    public function delete(Subtask $subtask): void
    {
        $subtask->delete();
    }
}
