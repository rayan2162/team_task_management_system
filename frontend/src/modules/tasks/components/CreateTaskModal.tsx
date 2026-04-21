import { useState } from 'react';
import Modal from '@/components/ui/Modal';
import { useCreateTask } from '@/hooks/useTasks';
import { extractApiError } from '@/hooks/useAuth';
import type { User } from '@/types';

interface Props {
  isOpen: boolean;
  onClose: () => void;
  projectId: number;
  members: User[];
}

export default function CreateTaskModal({ isOpen, onClose, projectId, members }: Props) {
  const [title, setTitle] = useState('');
  const [deadline, setDeadline] = useState('');
  const [assignedTo, setAssignedTo] = useState<string>('');
  const { mutate, isPending, error, reset } = useCreateTask(projectId);

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    mutate(
      {
        title,
        deadline: deadline || undefined,
        assigned_to: assignedTo ? Number(assignedTo) : undefined,
      },
      {
        onSuccess: () => {
          setTitle('');
          setDeadline('');
          setAssignedTo('');
          reset();
          onClose();
        },
      },
    );
  }

  function handleClose() {
    setTitle('');
    setDeadline('');
    setAssignedTo('');
    reset();
    onClose();
  }

  return (
    <Modal isOpen={isOpen} onClose={handleClose} title="Create Task">
      {error && (
        <div className="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
          {extractApiError(error)}
        </div>
      )}
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Title</label>
          <input
            type="text"
            value={title}
            onChange={(e) => setTitle(e.target.value)}
            required
            maxLength={150}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none"
          />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
          <input
            type="date"
            value={deadline}
            onChange={(e) => setDeadline(e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none"
          />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Assign To</label>
          <select
            value={assignedTo}
            onChange={(e) => setAssignedTo(e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none"
          >
            <option value="">Unassigned</option>
            {members.map((m) => (
              <option key={m.id} value={m.id}>{m.name}</option>
            ))}
          </select>
        </div>
        <div className="flex gap-3 justify-end">
          <button type="button" onClick={handleClose} className="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
            Cancel
          </button>
          <button
            type="submit"
            disabled={isPending}
            className="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 disabled:opacity-50"
          >
            {isPending ? 'Creating...' : 'Create Task'}
          </button>
        </div>
      </form>
    </Modal>
  );
}
