import { useState, useMemo } from 'react';
import { useProjects } from '@/hooks/useProjects';
import ProjectCard from './components/ProjectCard';
import CreateProjectModal from './components/CreateProjectModal';
import JoinProjectModal from './components/JoinProjectModal';
import LoadingSpinner from '@/components/ui/LoadingSpinner';

export default function ProjectsPage() {
  const { data: projects, isLoading } = useProjects();
  const [search, setSearch] = useState('');
  const [showCreate, setShowCreate] = useState(false);
  const [showJoin, setShowJoin] = useState(false);

  const filtered = useMemo(() => {
    if (!projects) return [];
    if (!search) return projects;
    const q = search.toLowerCase();
    return projects.filter((p) => p.name.toLowerCase().includes(q));
  }, [projects, search]);

  return (
    <div className="space-y-6 pb-12">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold text-white">My Projects</h1>
        <div className="flex gap-3">
          <button
            onClick={() => setShowJoin(true)}
            className="bg-white text-gray-900 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 transition-colors"
          >
            Join +
          </button>
          <button
            onClick={() => setShowCreate(true)}
            className="bg-white text-gray-900 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 transition-colors"
          >
            Create Project +
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

      {isLoading ? (
        <LoadingSpinner />
      ) : filtered.length === 0 ? (
        <div className="bg-white rounded-xl p-12 text-center">
          <p className="text-gray-500">
            {search ? 'No projects match your search.' : 'No projects yet. Create or join one!'}
          </p>
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {filtered.map((project) => (
            <ProjectCard key={project.id} project={project} />
          ))}
        </div>
      )}

      <CreateProjectModal isOpen={showCreate} onClose={() => setShowCreate(false)} />
      <JoinProjectModal isOpen={showJoin} onClose={() => setShowJoin(false)} />
    </div>
  );
}
