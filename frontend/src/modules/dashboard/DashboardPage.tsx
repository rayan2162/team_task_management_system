import { useDashboardStats, useDashboardAnalytics } from '@/hooks/useDashboard';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts';

export default function DashboardPage() {
  const { data: stats, isLoading: loadingStats } = useDashboardStats();
  const { data: analytics, isLoading: loadingAnalytics } = useDashboardAnalytics();

  if (loadingStats) return <LoadingSpinner />;

  const cards = [
    { label: 'Assigned Tasks', value: stats?.assigned_tasks ?? 0, color: 'bg-blue-500' },
    { label: 'Assigned Subtasks', value: stats?.assigned_subtasks ?? 0, color: 'bg-purple-500' },
    { label: 'Completed Tasks', value: stats?.completed_tasks ?? 0, color: 'bg-green-500' },
    { label: 'Completion Rate', value: `${stats?.completion_rate ?? 0}%`, color: 'bg-orange-500' },
  ];

  return (
    <div className="space-y-8 pb-12">
      <h1 className="text-3xl font-bold text-white">Dashboard</h1>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {cards.map((card) => (
          <div key={card.label} className="bg-white rounded-xl shadow-sm p-6">
            <p className="text-sm text-gray-500 font-medium">{card.label}</p>
            <p className="mt-2 text-3xl font-bold text-gray-900">{card.value}</p>
            <div className={`mt-3 h-1 w-12 rounded ${card.color}`} />
          </div>
        ))}
      </div>

      <div className="bg-white rounded-xl shadow-sm p-6">
        <h2 className="text-lg font-bold text-gray-900 mb-4">Completion Trend (Last 30 Days)</h2>
        {loadingAnalytics ? (
          <LoadingSpinner />
        ) : analytics && analytics.length > 0 ? (
          <ResponsiveContainer width="100%" height={300}>
            <BarChart data={analytics}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="date" tick={{ fontSize: 12 }} />
              <YAxis allowDecimals={false} />
              <Tooltip />
              <Bar dataKey="completed" fill="#8b5cf6" radius={[4, 4, 0, 0]} />
            </BarChart>
          </ResponsiveContainer>
        ) : (
          <p className="text-gray-500 text-sm py-8 text-center">No completion data yet. Start completing tasks!</p>
        )}
      </div>
    </div>
  );
}
