<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        return $project->hasMember($user->id);
    }

    public function manage(User $user, Project $project): bool
    {
        return $project->created_by === $user->id;
    }

    public function createTask(User $user, Project $project): bool
    {
        return $project->hasMember($user->id);
    }
}
