import { useState } from 'react';
import Modal from '@/components/ui/Modal';
import { useCreateProject } from '@/hooks/useProjects';
import { extractApiError } from '@/hooks/useAuth';

interface Props {
  isOpen: boolean;
  onClose: () => void;
}

export default function CreateProjectModal({ isOpen, onClose }: Props) {
  const [name, setName] = useState('');
  const { mutate, isPending, error, reset } = useCreateProject();

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    mutate({ name }, {
      onSuccess: () => {
        setName('');
        reset();
        onClose();
      },
    });
  }

  function handleClose() {
    setName('');
    reset();
    onClose();
  }

  return (
    <Modal isOpen={isOpen} onClose={handleClose} title="Create Project">
      {error && (
        <div className="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
          {extractApiError(error)}
        </div>
      )}
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
          <input
            type="text"
            value={name}
            onChange={(e) => setName(e.target.value)}
            required
            maxLength={150}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none"
            placeholder="Enter project name"
          />
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
            {isPending ? 'Creating...' : 'Create'}
          </button>
        </div>
      </form>
    </Modal>
  );
}
