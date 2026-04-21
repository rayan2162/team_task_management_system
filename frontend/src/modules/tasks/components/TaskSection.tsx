import type { Task } from '@/types';
import StatusBadge from '@/components/ui/StatusBadge';
import Avatar from '@/components/ui/Avatar';
import SubtaskRow from './SubtaskRow';

interface TaskSectionProps {
  task: Task;
  index: number;
}

export default function TaskSection({ task, index }: TaskSectionProps) {
  return (
    <div className="bg-gray-900 rounded-2xl overflow-hidden">
      <div className="px-6 py-4 flex items-center justify-between">
        <h3 className="text-white font-bold text-base">
          {index + 1}. {task.title}
        </h3>
        <div className="flex items-center gap-3">
          {task.assignee && (
            <Avatar src={task.assignee.avatar} name={task.assignee.name} size="sm" />
          )}
          <StatusBadge status={task.status} />
        </div>
      </div>

      {task.subtasks && task.subtasks.length > 0 && (
        <div className="px-4 pb-4 space-y-2">
          {task.subtasks.map((subtask) => (
            <SubtaskRow key={subtask.id} subtask={subtask} />
          ))}
        </div>
      )}
    </div>
  );
}
