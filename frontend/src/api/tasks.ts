import client from './client';
import type { ApiResponse, Task } from '@/types';

export const tasksApi = {
  listByProject: (projectId: number, filters?: Record<string, string>) =>
    client.get<ApiResponse<Task[]>>(`/projects/${projectId}/tasks`, { params: filters }).then((r) => r.data),

  get: (id: number) =>
    client.get<ApiResponse<Task>>(`/tasks/${id}`).then((r) => r.data),

  create: (projectId: number, data: { title: string; status?: string; deadline?: string; assigned_to?: number | null }) =>
    client.post<ApiResponse<Task>>(`/projects/${projectId}/tasks`, data).then((r) => r.data),

  update: (id: number, data: Partial<{ title: string; status: string; deadline: string; assigned_to: number | null }>) =>
    client.put<ApiResponse<Task>>(`/tasks/${id}`, data).then((r) => r.data),

  updateStatus: (id: number, status: string) =>
    client.patch<ApiResponse<Task>>(`/tasks/${id}/status`, { status }).then((r) => r.data),

  delete: (id: number) =>
    client.delete<ApiResponse<null>>(`/tasks/${id}`).then((r) => r.data),
};
