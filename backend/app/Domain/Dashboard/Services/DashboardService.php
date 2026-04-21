<?php

namespace App\Domain\Dashboard\Services;

use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getStats(int $userId): array
    {
        $assignedTasks = Task::where('assigned_to', $userId)->count();
        $assignedSubtasks = Subtask::where('assigned_to', $userId)->count();
        $completedTasks = Task::where('assigned_to', $userId)->where('status', 'done')->count();

        $completionRate = $assignedTasks > 0
            ? round(($completedTasks / $assignedTasks) * 100)
            : 0;

        return [
            'assigned_tasks' => $assignedTasks,
            'assigned_subtasks' => $assignedSubtasks,
            'completed_tasks' => $completedTasks,
            'completion_rate' => $completionRate,
        ];
    }

    public function getAnalytics(int $userId): array
    {
        $data = Task::where('assigned_to', $userId)
            ->where('status', 'done')
            ->where('updated_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(updated_at) as date'), DB::raw('COUNT(*) as completed'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();

        return $data;
    }
}
