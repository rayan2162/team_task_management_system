import { useMutation } from '@tanstack/react-query';
import { authApi } from '@/api/auth';
import { useAuthStore } from '@/stores/authStore';
import { useNavigate } from 'react-router-dom';
import type { LoginPayload, RegisterPayload } from '@/types';
import type { AxiosError } from 'axios';
import type { ApiResponse } from '@/types';

export function useLogin() {
  const setAuth = useAuthStore((s) => s.setAuth);
  const navigate = useNavigate();

  return useMutation({
    mutationFn: (data: LoginPayload) => authApi.login(data),
    onSuccess: (res) => {
      setAuth(res.data.token, res.data.user);
      navigate('/');
    },
  });
}

export function useRegister() {
  const setAuth = useAuthStore((s) => s.setAuth);
  const navigate = useNavigate();

  return useMutation({
    mutationFn: (data: RegisterPayload) => authApi.register(data),
    onSuccess: (res) => {
      setAuth(res.data.token, res.data.user);
      navigate('/');
    },
  });
}

export function useLogout() {
  const logout = useAuthStore((s) => s.logout);
  const navigate = useNavigate();

  return useMutation({
    mutationFn: () => authApi.logout(),
    onSettled: () => {
      logout();
      navigate('/login');
    },
  });
}

export function extractApiError(error: unknown): string {
  const axiosError = error as AxiosError<ApiResponse<null>>;
  if (axiosError.response?.data?.message) {
    return axiosError.response.data.message;
  }
  if (axiosError.response?.data?.errors) {
    const firstErrors = Object.values(axiosError.response.data.errors);
    return firstErrors[0]?.[0] ?? 'An error occurred.';
  }
  return 'An unexpected error occurred.';
}
