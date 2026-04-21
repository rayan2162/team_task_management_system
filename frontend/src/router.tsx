import { createBrowserRouter } from 'react-router-dom';
import AuthGuard from '@/components/guards/AuthGuard';
import AppLayout from '@/components/layout/AppLayout';
import LoginPage from '@/modules/auth/LoginPage';
import RegisterPage from '@/modules/auth/RegisterPage';
import DashboardPage from '@/modules/dashboard/DashboardPage';
import ProjectsPage from '@/modules/projects/ProjectsPage';
import ProjectDetailPage from '@/modules/projects/ProjectDetailPage';
import ProfilePage from '@/modules/profile/ProfilePage';

export const router = createBrowserRouter([
  {
    path: '/login',
    element: <LoginPage />,
  },
  {
    path: '/register',
    element: <RegisterPage />,
  },
  {
    element: <AuthGuard />,
    children: [
      {
        element: <AppLayout />,
        children: [
          { path: '/', element: <DashboardPage /> },
          { path: '/projects', element: <ProjectsPage /> },
          { path: '/projects/:id', element: <ProjectDetailPage /> },
          { path: '/profile', element: <ProfilePage /> },
        ],
      },
    ],
  },
]);
