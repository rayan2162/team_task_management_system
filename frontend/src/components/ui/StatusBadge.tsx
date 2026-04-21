interface StatusBadgeProps {
  status: 'pending' | 'working' | 'done';
}

const statusConfig = {
  pending: { label: 'Pending', className: 'bg-yellow-500 text-white' },
  working: { label: 'Running', className: 'bg-purple-500 text-white' },
  done: { label: 'DONE', className: 'bg-green-500 text-white' },
} as const;

export default function StatusBadge({ status }: StatusBadgeProps) {
  const config = statusConfig[status];
  return (
    <span className={`px-3 py-1 rounded-full text-xs font-bold uppercase ${config.className}`}>
      {config.label}
    </span>
  );
}
