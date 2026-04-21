import { useMutation, useQueryClient } from '@tanstack/react-query';
import { subtasksApi } from '@/api/subtasks';

export function useCreateSubtask(taskId: number) {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (data: { body: string; status?: string; deadline?: string; assigned_to?: number | null }) =>
      subtasksApi.create(taskId, data),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['tasks'] });
      qc.invalidateQueries({ queryKey: ['projects'] });
    },
  });
}

export function useUpdateSubtaskStatus() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: ({ id, status }: { id: number; status: string }) =>
      subtasksApi.updateStatus(id, status),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['tasks'] });
      qc.invalidateQueries({ queryKey: ['projects'] });
    },
  });
}

export function useDeleteSubtask() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (id: number) => subtasksApi.delete(id),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['tasks'] });
      qc.invalidateQueries({ queryKey: ['projects'] });
    },
  });
}
