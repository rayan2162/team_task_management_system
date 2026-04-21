import { useState } from 'react';
import Modal from '@/components/ui/Modal';
import { useJoinProject } from '@/hooks/useProjects';
import { extractApiError } from '@/hooks/useAuth';

interface Props {
  isOpen: boolean;
  onClose: () => void;
}

export default function JoinProjectModal({ isOpen, onClose }: Props) {
  const [code, setCode] = useState('');
  const { mutate, isPending, error, reset } = useJoinProject();

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    mutate(code, {
      onSuccess: () => {
        setCode('');
        reset();
        onClose();
      },
    });
  }

  function handleClose() {
    setCode('');
    reset();
    onClose();
  }

  return (
    <Modal isOpen={isOpen} onClose={handleClose} title="Join Project">
      {error && (
        <div className="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
          {extractApiError(error)}
        </div>
      )}
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Project Code</label>
          <input
            type="text"
            value={code}
            onChange={(e) => setCode(e.target.value.toUpperCase())}
            required
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none font-mono tracking-wider"
            placeholder="PRJ-XXXXXX"
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
            {isPending ? 'Joining...' : 'Join'}
          </button>
        </div>
      </form>
    </Modal>
  );
}
