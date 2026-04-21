import type { Project } from '@/types';
import { useNavigate } from 'react-router-dom';

interface ProjectCardProps {
  project: Project;
}

export default function ProjectCard({ project }: ProjectCardProps) {
  const navigate = useNavigate();

  return (
    <div className="bg-gray-900 rounded-2xl p-5 text-white relative overflow-hidden group">
      <div className="absolute top-3 right-3">
        <span className={`w-3 h-3 rounded-full inline-block ${
          project.status === 'active' ? 'bg-green-400' : project.status === 'done' ? 'bg-blue-400' : 'bg-gray-500'
        }`} />
      </div>

      <div className="flex items-start gap-4">
        <div className="w-16 h-16 rounded-xl bg-gradient-to-br from-orange-400 to-pink-500 flex-shrink-0" />
        <div className="flex-1 min-w-0">
          <h3 className="font-bold text-base truncate">{project.name}</h3>
          <p className="text-gray-400 text-sm mt-0.5">
            Created by: {project.creator?.name ?? 'Unknown'}
          </p>
          <button
            onClick={() => navigate(`/projects/${project.id}`)}
            className="mt-3 bg-white text-gray-900 px-4 py-1.5 rounded-lg text-sm font-medium hover:bg-gray-100 transition-colors"
          >
            Go to Project →
          </button>
        </div>
      </div>
    </div>
  );
}
