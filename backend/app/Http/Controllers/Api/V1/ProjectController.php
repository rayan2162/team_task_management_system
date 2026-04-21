<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Project\Services\ProjectService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\JoinProjectRequest;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ProjectService $projectService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $projects = $this->projectService->list($request->user()->id);

        return $this->success(
            ProjectResource::collection($projects),
            'Projects retrieved.',
        );
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->create(
            $request->validated(),
            $request->user()->id,
        );

        return $this->created(
            new ProjectResource($project),
            'Project created successfully.',
        );
    }

    public function show(Request $request, Project $project): JsonResponse
    {
        Gate::authorize('view', $project);

        $project = $this->projectService->show($project->id);

        return $this->success(
            new ProjectResource($project),
            'Project retrieved.',
        );
    }

    public function join(JoinProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->join(
            $request->validated('code'),
            $request->user()->id,
        );

        return $this->success(
            new ProjectResource($project),
            'Joined project successfully.',
        );
    }

    public function archive(Request $request, Project $project): JsonResponse
    {
        Gate::authorize('manage', $project);

        $project = $this->projectService->archive($project);

        return $this->success(
            new ProjectResource($project),
            'Project archived.',
        );
    }
}
