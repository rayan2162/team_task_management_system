<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Rayan',
            'email' => 'rayan@example.com',
            'password' => 'password',
        ]);

        $member = User::factory()->create([
            'name' => 'Team Member',
            'email' => 'member@example.com',
            'password' => 'password',
        ]);

        $project = Project::create([
            'name' => 'Cashflow App (Issues)',
            'created_by' => $admin->id,
        ]);

        $project->members()->attach($admin->id, ['joined_at' => now()]);
        $project->members()->attach($member->id, ['joined_at' => now()]);

        $task1 = Task::create([
            'project_id' => $project->id,
            'title' => "Can't Login with yahoo mail",
            'status' => 'done',
            'deadline' => '2026-03-15',
            'created_by' => $admin->id,
            'assigned_to' => $member->id,
        ]);

        Subtask::create([
            'task_id' => $task1->id,
            'body' => 'Modify Config files',
            'status' => 'done',
            'deadline' => '2026-02-26',
            'assigned_to' => $member->id,
        ]);

        Subtask::create([
            'task_id' => $task1->id,
            'body' => 'Check Yahoo Docs for cross-origin',
            'status' => 'working',
            'deadline' => '2026-03-18',
            'assigned_to' => $admin->id,
        ]);

        $task2 = Task::create([
            'project_id' => $project->id,
            'title' => 'Data dropping during submission',
            'status' => 'working',
            'deadline' => '2026-04-10',
            'created_by' => $admin->id,
            'assigned_to' => $admin->id,
        ]);

        Subtask::create([
            'task_id' => $task2->id,
            'body' => 'Implement Transitional logic',
            'status' => 'working',
            'deadline' => '2026-04-03',
            'assigned_to' => $member->id,
        ]);

        foreach (['UI/UX', 'Devops', 'Backend', 'Frontend'] as $variant) {
            $p = Project::create([
                'name' => "Cashflow App ($variant)",
                'created_by' => $admin->id,
            ]);
            $p->members()->attach($admin->id, ['joined_at' => now()]);
        }
    }
}
