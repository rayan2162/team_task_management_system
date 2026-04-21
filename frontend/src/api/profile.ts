import client from './client';
import type { ApiResponse, User } from '@/types';

export const profileApi = {
  get: () =>
    client.get<ApiResponse<User>>('/profile').then((r) => r.data),

  update: (data: { name?: string; email?: string }) =>
    client.patch<ApiResponse<User>>('/profile', data).then((r) => r.data),

  uploadAvatar: (file: File) => {
    const formData = new FormData();
    formData.append('avatar', file);
    return client.post<ApiResponse<User>>('/profile/avatar', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }).then((r) => r.data);
  },
};
