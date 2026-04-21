import { useState, useMemo } from 'react';
import { useParams } from 'react-router-dom';
import { useProject } from '@/hooks/useProjects';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import TaskSection from '@/modules/tasks/components/TaskSection';
import CreateTaskModal from '@/modules/tasks/components/CreateTaskModal';
import CreateSubtaskModal from '@/modules/tasks/components/CreateSubtaskModal';

export default function ProjectDetailPage() {
  const { id } = useParams<{ id: string }>();
  const projectId = Number(id);
  const { data: project, isLoading } = useProject(projectId);
  const [search, setSearch] = useState('');
  const [showCreateTask, setShowCreateTask] = useState(false);
  const [subtaskTaskId, setSubtaskTaskId] = useState<number | null>(null);

  const filteredTasks = useMemo(() => {
    if (!project?.tasks) return [];
    if (!search) return project.tasks;
    const q = search.toLowerCase();
    return project.tasks.filter(
      (t) =>
        t.title.toLowerCase().includes(q) ||
        t.subtasks?.some((s) => s.body.toLowerCase().includes(q)),
    );
  }, [project?.tasks, search]);

  if (isLoading || !project) return <LoadingSpinner />;

  return (
    <div className="space-y-6 pb-12">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-white">{project.name}</h1>
          <p className="text-white/70 text-sm mt-1">
            Code: <span className="font-mono">{project.code}</span> · {project.members?.length ?? 0} members
          </p>
        </div>
        <div className="flex gap-3">
          <button
            onClick={() => setShowCreateTask(true)}
            className="bg-white text-gray-900 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 transition-colors"
          >
            Add Task +
          </button>
        </div>
      </div>

      <div className="flex justify-end">
        <div className="relative">
          <input
            type="text"
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="bg-white border border-gray-200 rounded-lg px-4 py-2 pr-10 text-sm w-64 focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none"
            placeholder="Search"
          />
          <span className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
        </div>
      </div>

      {filteredTasks.length === 0 ? (
        <div className="bg-white rounded-xl p-12 text-center">
          <p className="text-gray-500">
            {search ? 'No tasks match your search.' : 'No tasks yet. Create one!'}
          </p>
        </div>
      ) : (
        <div className="space-y-4">
          {filteredTasks.map((task, i) => (
            <div key={task.id}>
              <TaskSection task={task} index={i} />
              <button
                onClick={() => setSubtaskTaskId(task.id)}
                className="mt-2 ml-4 text-xs text-purple-600 hover:text-purple-700 font-medium"
              >
                + Add Subtask
              </button>
            </div>
          ))}
        </div>
      )}

      <CreateTaskModal
        isOpen={showCreateTask}
        onClose={() => setShowCreateTask(false)}
        projectId={projectId}
        members={project.members ?? []}
      />

      {subtaskTaskId && (
        <CreateSubtaskModal
          isOpen={!!subtaskTaskId}
          onClose={() => setSubtaskTaskId(null)}
          taskId={subtaskTaskId}
          members={project.members ?? []}
        />
      )}
    </div>
  );
}
