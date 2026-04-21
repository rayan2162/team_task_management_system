import type { Subtask } from '@/types';
import Avatar from '@/components/ui/Avatar';
import { useUpdateSubtaskStatus } from '@/hooks/useSubtasks';

interface SubtaskRowProps {
  subtask: Subtask;
}

export default function SubtaskRow({ subtask }: SubtaskRowProps) {
  const { mutate: updateStatus } = useUpdateSubtaskStatus();

  function handleStatusChange(e: React.ChangeEvent<HTMLSelectElement>) {
    updateStatus({ id: subtask.id, status: e.target.value });
  }

  return (
    <div className="bg-gray-100 rounded-xl px-4 py-3 flex items-center gap-3">
      <div className="w-5 h-5 rounded-full border-2 border-gray-400 flex-shrink-0 flex items-center justify-center">
        {subtask.status === 'done' && <div className="w-2.5 h-2.5 rounded-full bg-green-500" />}
        {subtask.status === 'working' && <div className="w-2.5 h-2.5 rounded-full bg-purple-500" />}
      </div>

      <span className="text-sm text-gray-800 flex-1 truncate">{subtask.body}</span>

      <span className="text-gray-400 mx-1">|</span>

      {subtask.assignee && (
        <Avatar src={subtask.assignee.avatar} name={subtask.assignee.name} size="sm" />
      )}

      <span className="text-gray-400 mx-1">|</span>

      <span className="text-sm text-gray-600 whitespace-nowrap">
        {subtask.deadline ? new Date(subtask.deadline).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '—'}
      </span>

      <span className="text-gray-400 mx-1">|</span>

      <select
        value={subtask.status}
        onChange={handleStatusChange}
        className="text-xs bg-white border border-gray-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-purple-500 outline-none cursor-pointer"
      >
        <option value="pending">Pending</option>
        <option value="working">Working</option>
        <option value="done">Done</option>
      </select>
    </div>
  );
}
