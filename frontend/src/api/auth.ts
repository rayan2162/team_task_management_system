import client from './client';
import type { ApiResponse, AuthResponse, LoginPayload, RegisterPayload, User } from '@/types';

export const authApi = {
  register: (data: RegisterPayload) =>
    client.post<ApiResponse<AuthResponse>>('/auth/register', data).then((r) => r.data),

  login: (data: LoginPayload) =>
    client.post<ApiResponse<AuthResponse>>('/auth/login', data).then((r) => r.data),

  logout: () =>
    client.post<ApiResponse<null>>('/auth/logout').then((r) => r.data),

  me: () =>
    client.get<ApiResponse<User>>('/auth/me').then((r) => r.data),
};
