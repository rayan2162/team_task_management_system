import client from './client';
import type { ApiResponse, DashboardAnalytics, DashboardStats } from '@/types';

export const dashboardApi = {
  getStats: () =>
    client.get<ApiResponse<DashboardStats>>('/dashboard').then((r) => r.data),

  getAnalytics: () =>
    client.get<ApiResponse<DashboardAnalytics[]>>('/dashboard/analytics').then((r) => r.data),
};
