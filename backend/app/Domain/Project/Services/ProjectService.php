<?php

namespace App\Domain\Project\Services;

use App\Domain\Project\Repositories\ProjectRepository;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class ProjectService
{
    public function __construct(
        private readonly ProjectRepository $repository,
    ) {}

    public function list(int $userId): Collection
    {
        return $this->repository->getUserProjects($userId);
    }

    public function create(array $data, int $userId): Project
    {
        $project = $this->repository->create([
            'name' => $data['name'],
            'created_by' => $userId,
        ]);

        $this->repository->addMember($project, $userId);

        return $project->load('creator:id,name');
    }

    public function show(int $projectId): Project
    {
        return $this->repository->findWithDetails($projectId);
    }

    public function join(string $code, int $userId): Project
    {
        $project = $this->repository->findByCode($code);

        if (! $project) {
            throw ValidationException::withMessages([
                'code' => ['Invalid project code.'],
            ]);
        }

        if ($this->repository->isMember($project->id, $userId)) {
            throw ValidationException::withMessages([
                'code' => ['You are already a member of this project.'],
            ]);
        }

        try {
            $this->repository->addMember($project, $userId);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'Unique') || str_contains($e->getMessage(), 'unique') || str_contains($e->getMessage(), 'duplicate')) {
                throw ValidationException::withMessages([
                    'code' => ['You are already a member of this project.'],
                ]);
            }
            throw $e;
        }

        return $project->load('creator:id,name');
    }

    public function archive(Project $project): Project
    {
        $project->update(['status' => 'archived']);
        return $project->fresh();
    }
}
