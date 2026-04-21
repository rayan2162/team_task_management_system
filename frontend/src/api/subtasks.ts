import client from './client';
import type { ApiResponse, Subtask } from '@/types';

export const subtasksApi = {
  listByTask: (taskId: number) =>
    client.get<ApiResponse<Subtask[]>>(`/tasks/${taskId}/subtasks`).then((r) => r.data),

  create: (taskId: number, data: { body: string; status?: string; deadline?: string; assigned_to?: number | null }) =>
    client.post<ApiResponse<Subtask>>(`/tasks/${taskId}/subtasks`, data).then((r) => r.data),

  update: (id: number, data: Partial<{ body: string; status: string; deadline: string; assigned_to: number | null }>) =>
    client.put<ApiResponse<Subtask>>(`/subtasks/${id}`, data).then((r) => r.data),

  updateStatus: (id: number, status: string) =>
    client.patch<ApiResponse<Subtask>>(`/subtasks/${id}/status`, { status }).then((r) => r.data),

  delete: (id: number) =>
    client.delete<ApiResponse<null>>(`/subtasks/${id}`).then((r) => r.data),
};
