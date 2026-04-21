import { useState, useRef } from 'react';
import { useAuthStore } from '@/stores/authStore';
import { useUpdateProfile, useUploadAvatar } from '@/hooks/useProfile';
import Avatar from '@/components/ui/Avatar';
import { extractApiError } from '@/hooks/useAuth';

export default function ProfilePage() {
  const user = useAuthStore((s) => s.user);
  const [name, setName] = useState(user?.name ?? '');
  const [email, setEmail] = useState(user?.email ?? '');
  const fileRef = useRef<HTMLInputElement>(null);

  const { mutate: updateProfile, isPending: updating, error: updateError, isSuccess: updateSuccess } = useUpdateProfile();
  const { mutate: uploadAvatar, isPending: uploading } = useUploadAvatar();

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    updateProfile({ name, email });
  }

  function handleAvatarChange(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0];
    if (file) {
      uploadAvatar(file);
    }
  }

  if (!user) return null;

  return (
    <div className="space-y-8 pb-12">
      <h1 className="text-3xl font-bold text-white">Profile</h1>

      <div className="bg-white rounded-xl shadow-sm p-8 max-w-xl">
        <div className="flex items-center gap-6 mb-8">
          <div className="relative">
            <Avatar src={user.avatar} name={user.name} size="lg" />
            <button
              onClick={() => fileRef.current?.click()}
              disabled={uploading}
              className="absolute -bottom-1 -right-1 bg-purple-600 text-white w-6 h-6 rounded-full text-xs flex items-center justify-center hover:bg-purple-700 disabled:opacity-50"
            >
              {uploading ? '…' : '✎'}
            </button>
            <input
              ref={fileRef}
              type="file"
              accept="image/*"
              onChange={handleAvatarChange}
              className="hidden"
            />
          </div>
          <div>
            <h2 className="text-lg font-bold text-gray-900">{user.name}</h2>
            <p className="text-gray-500 text-sm">{user.email}</p>
          </div>
        </div>

        {updateError && (
          <div className="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            {extractApiError(updateError)}
          </div>
        )}

        {updateSuccess && (
          <div className="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
            Profile updated successfully.
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input
              type="text"
              value={name}
              onChange={(e) => setName(e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none"
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none"
            />
          </div>
          <button
            type="submit"
            disabled={updating}
            className="bg-gray-900 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-800 disabled:opacity-50 transition-colors"
          >
            {updating ? 'Saving...' : 'Save Changes'}
          </button>
        </form>
      </div>
    </div>
  );
}
