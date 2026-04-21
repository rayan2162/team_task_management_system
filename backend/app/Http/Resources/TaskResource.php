<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'title' => $this->title,
            'status' => $this->status,
            'deadline' => $this->deadline?->toDateString(),
            'created_by' => $this->created_by,
            'assigned_to' => $this->assigned_to,
            'creator' => new UserResource($this->whenLoaded('creator')),
            'assignee' => new UserResource($this->whenLoaded('assignee')),
            'project' => new ProjectResource($this->whenLoaded('project')),
            'subtasks' => SubtaskResource::collection($this->whenLoaded('subtasks')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
