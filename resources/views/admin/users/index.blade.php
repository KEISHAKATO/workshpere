<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Users</h2>
    </x-slot>

    <div class="max-w-6xl mx-auto p-6">
        @if(session('status')) <div class="mb-4 p-2 bg-green-50 text-green-700 rounded">{{ session('status') }}</div> @endif

        <form method="GET" class="mb-4 flex gap-2">
            <select name="role" class="border rounded px-2 py-1">
                <option value="">All roles</option>
                <option value="seeker"   @selected(($filters['role'] ?? '')==='seeker')>Seeker</option>
                <option value="employer" @selected(($filters['role'] ?? '')==='employer')>Employer</option>
                <option value="admin"    @selected(($filters['role'] ?? '')==='admin')>Admin</option>
            </select>
            <button class="px-3 py-1 rounded bg-gray-100">Filter</button>
        </form>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-2 text-left">User</th>
                        <th class="px-4 py-2">Role</th>
                        <th class="px-4 py-2">Flags</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        <tr class="border-t">
                            <td class="px-4 py-2">
                                <div class="font-medium">{{ $u->name }}</div>
                                <div class="text-xs text-gray-500">{{ $u->email }}</div>
                            </td>
                            <td class="px-4 py-2">{{ ucfirst($u->role) }}</td>
                            <td class="px-4 py-2">
                                @if(!$u->is_active)
                                    <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Suspended</span>
                                @endif
                                @if($u->is_flagged)
                                    <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Flagged</span>
                                @endif
                                @if($u->is_active && !$u->is_flagged)
                                    <span class="text-xs text-gray-500">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                <form class="inline" method="POST" action="{{ route('admin.users.toggleActive', $u) }}">
                                    @csrf @method('PUT')
                                    <button class="px-2 py-1 rounded bg-gray-100">
                                        {{ $u->is_active ? 'Suspend' : 'Activate' }}
                                    </button>
                                </form>
                                <form class="inline ml-2" method="POST" action="{{ route('admin.users.toggleFlag', $u) }}">
                                    @csrf @method('PUT')
                                    <button class="px-2 py-1 rounded bg-gray-100">
                                        {{ $u->is_flagged ? 'Unflag' : 'Flag' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if($users->isEmpty())
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">No users.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $users->withQueryString()->links() }}</div>
    </div>
</x-app-layout>
