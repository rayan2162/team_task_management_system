<?php

namespace App\Policies;

use App\Models\Subtask;
use App\Models\User;

class SubtaskPolicy
{
    public function view(User $user, Subtask $subtask): bool
    {
        return $subtask->task->project->hasMember($user->id);
    }

    public function update(User $user, Subtask $subtask): bool
    {
        return $subtask->task->project->hasMember($user->id);
    }

    public function updateStatus(User $user, Subtask $subtask): bool
    {
        $project = $subtask->task->project;

        return $subtask->assigned_to === $user->id
            || $project->created_by === $user->id;
    }

    public function delete(User $user, Subtask $subtask): bool
    {
        return $subtask->task->project->hasMember($user->id);
    }
}
