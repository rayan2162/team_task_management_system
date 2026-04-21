export interface User {
  id: number;
  name: string;
  email: string;
  avatar: string | null;
  created_at: string;
}

export interface Project {
  id: number;
  code: string;
  name: string;
  status: 'active' | 'archived' | 'done';
  created_by: number;
  creator?: User;
  members?: User[];
  tasks?: Task[];
  created_at: string;
  updated_at: string;
}

export interface Task {
  id: number;
  project_id: number;
  title: string;
  status: 'pending' | 'working' | 'done';
  deadline: string | null;
  created_by: number;
  assigned_to: number | null;
  creator?: User;
  assignee?: User;
  project?: Project;
  subtasks?: Subtask[];
  created_at: string;
  updated_at: string;
}

export interface Subtask {
  id: number;
  task_id: number;
  body: string;
  status: 'pending' | 'working' | 'done';
  deadline: string | null;
  assigned_to: number | null;
  assignee?: User;
  created_at: string;
  updated_at: string;
}

export interface DashboardStats {
  assigned_tasks: number;
  assigned_subtasks: number;
  completed_tasks: number;
  completion_rate: number;
}

export interface DashboardAnalytics {
  date: string;
  completed: number;
}

export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
  errors?: Record<string, string[]>;
}

export interface LoginPayload {
  email: string;
  password: string;
}

export interface RegisterPayload {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}

export interface AuthResponse {
  user: User;
  token: string;
}
