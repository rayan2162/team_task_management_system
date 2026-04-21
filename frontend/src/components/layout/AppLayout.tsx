import { Outlet } from 'react-router-dom';
import Navbar from './Navbar';

export default function AppLayout() {
  return (
    <div className="min-h-screen bg-gray-50">
      <Navbar />
      <div className="bg-gradient-to-r from-purple-900 via-pink-800 to-rose-700 h-32" />
      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16">
        <Outlet />
      </main>
    </div>
  );
}
