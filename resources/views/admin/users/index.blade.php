<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Users</h2></x-slot>

    <div class="max-w-6xl mx-auto p-4">
        @if(session('status')) <div class="alert alert-success mb-4"><span>{{ session('status') }}</span></div> @endif

        <form method="GET" class="mb-4 flex gap-2 items-end">
            <div class="form-control">
                <label class="label"><span class="label-text">Role</span></label>
                <select name="role" class="select select-bordered">
                    <option value="">All roles</option>
                    <option value="seeker"   @selected(($filters['role'] ?? '')==='seeker')>Seeker</option>
                    <option value="employer" @selected(($filters['role'] ?? '')==='employer')>Employer</option>
                    <option value="admin"    @selected(($filters['role'] ?? '')==='admin')>Admin</option>
                </select>
            </div>
            <button class="btn">Filter</button>
        </form>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table text-sm">
                        <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Flags</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($users as $u)
                            <tr>
                                <td>
                                    <div class="font-medium">{{ $u->name }}</div>
                                    <div class="text-xs opacity-70">{{ $u->email }}</div>
                                </td>
                                <td>{{ ucfirst($u->role) }}</td>
                                <td>
                                    @if(!$u->is_active)
                                        <span class="badge badge-error">Suspended</span>
                                    @endif
                                    @if($u->is_flagged)
                                        <span class="badge badge-warning">Flagged</span>
                                    @endif
                                    @if($u->is_active && !$u->is_flagged)
                                        <span class="opacity-60 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <form class="inline" method="POST" action="{{ route('admin.users.toggleActive', $u) }}">
                                        @csrf @method('PUT')
                                        <button class="btn btn-sm">
                                            {{ $u->is_active ? 'Suspend' : 'Activate' }}
                                        </button>
                                    </form>
                                    <form class="inline ml-2" method="POST" action="{{ route('admin.users.toggleFlag', $u) }}">
                                        @csrf @method('PUT')
                                        <button class="btn btn-sm"> {{ $u->is_flagged ? 'Unflag' : 'Flag' }} </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center opacity-70 py-6">No users.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">{{ $users->withQueryString()->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
