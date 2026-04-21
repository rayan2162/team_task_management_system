import client from './client';
import type { ApiResponse, Project } from '@/types';

export const projectsApi = {
  list: () =>
    client.get<ApiResponse<Project[]>>('/projects').then((r) => r.data),

  get: (id: number) =>
    client.get<ApiResponse<Project>>(`/projects/${id}`).then((r) => r.data),

  create: (data: { name: string }) =>
    client.post<ApiResponse<Project>>('/projects', data).then((r) => r.data),

  join: (code: string) =>
    client.post<ApiResponse<Project>>('/projects/join', { code }).then((r) => r.data),

  archive: (id: number) =>
    client.patch<ApiResponse<Project>>(`/projects/${id}/archive`).then((r) => r.data),
};
