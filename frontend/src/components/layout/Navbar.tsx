import { Link, useNavigate } from 'react-router-dom';
import { useAuthStore } from '@/stores/authStore';
import { useLogout } from '@/hooks/useAuth';
import { useState, useRef, useEffect } from 'react';

export default function Navbar() {
  const user = useAuthStore((s) => s.user);
  const { mutate: logout } = useLogout();
  const [menuOpen, setMenuOpen] = useState(false);
  const menuRef = useRef<HTMLDivElement>(null);
  const navigate = useNavigate();

  useEffect(() => {
    function handleClickOutside(e: MouseEvent) {
      if (menuRef.current && !menuRef.current.contains(e.target as Node)) {
        setMenuOpen(false);
      }
    }
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  return (
    <nav className="bg-gray-900/80 backdrop-blur-sm border-b border-gray-700">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16">
          <div className="flex items-center gap-8">
            <Link to="/" className="text-white font-bold text-lg">
              TaskFlow
            </Link>
            <div className="flex gap-6">
              <Link to="/" className="text-gray-300 hover:text-white transition-colors text-sm font-medium">
                Home
              </Link>
              <Link to="/projects" className="text-gray-300 hover:text-white transition-colors text-sm font-medium">
                My Projects
              </Link>
            </div>
          </div>

          <div className="relative" ref={menuRef}>
            <button
              onClick={() => setMenuOpen(!menuOpen)}
              className="flex items-center gap-2 text-gray-300 hover:text-white transition-colors"
            >
              <span className="text-sm font-medium">{user?.name}</span>
              <span className="text-xs">→</span>
            </button>

            {menuOpen && (
              <div className="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-xl border border-gray-700 py-1 z-50">
                <button
                  onClick={() => { navigate('/profile'); setMenuOpen(false); }}
                  className="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white"
                >
                  Profile
                </button>
                <button
                  onClick={() => { logout(); setMenuOpen(false); }}
                  className="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-gray-700"
                >
                  Logout
                </button>
              </div>
            )}
          </div>
        </div>
      </div>
    </nav>
  );
}
