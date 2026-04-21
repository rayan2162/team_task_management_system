<?php

namespace App\Domain\Project\Repositories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ProjectRepository
{
    public function getUserProjects(int $userId): Collection
    {
        return Project::whereHas('members', fn ($q) => $q->where('users.id', $userId))
            ->with('creator:id,name')
            ->latest()
            ->get();
    }

    public function findWithDetails(int $projectId): Project
    {
        return Project::with(['members:id,name,avatar', 'tasks' => function ($q) {
            $q->with(['assignee:id,name,avatar', 'subtasks.assignee:id,name,avatar'])
                ->latest();
        }])->findOrFail($projectId);
    }

    public function findByCode(string $code): ?Project
    {
        return Project::where('code', $code)->first();
    }

    public function isMember(int $projectId, int $userId): bool
    {
        return Project::where('id', $projectId)
            ->whereHas('members', fn ($q) => $q->where('users.id', $userId))
            ->exists();
    }

    public function addMember(Project $project, int $userId): void
    {
        $project->members()->attach($userId, ['joined_at' => now()]);
    }

    public function create(array $data): Project
    {
        return Project::create($data);
    }
}
