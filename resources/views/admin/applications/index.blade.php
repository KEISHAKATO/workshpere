<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Applications</h2></x-slot>

    <div class="max-w-6xl mx-auto p-4">
        @if(session('status')) <div class="alert alert-success mb-4"><span>{{ session('status') }}</span></div> @endif

        <form method="GET" class="mb-4 flex gap-2 items-end">
            <div class="form-control">
                <label class="label"><span class="label-text">Status</span></label>
                <select name="status" class="select select-bordered">
                    <option value="">All statuses</option>
                    <option value="pending"  @selected(($filters['status'] ?? '')==='pending')>Pending</option>
                    <option value="accepted" @selected(($filters['status'] ?? '')==='accepted')>Accepted</option>
                    <option value="rejected" @selected(($filters['status'] ?? '')==='rejected')>Rejected</option>
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
                            <th>Applicant</th>
                            <th>Job</th>
                            <th>Status</th>
                            <th>Applied</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($apps as $app)
                            <tr>
                                <td>
                                    <div class="font-medium">{{ $app->seeker->name }}</div>
                                    <div class="text-xs opacity-70">{{ $app->seeker->email }}</div>
                                </td>
                                <td>{{ $app->job->title }}</td>
                                <td>{{ ucfirst($app->status) }}</td>
                                <td>{{ $app->created_at->diffForHumans() }}</td>
                                <td class="text-right">
                                    <form class="inline" method="POST" action="{{ route('admin.applications.updateStatus', $app) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="{{ $app->status === 'pending' ? 'accepted' : 'pending' }}">
                                        <button class="btn btn-sm">
                                            {{ $app->status === 'pending' ? 'Accept' : 'Set Pending' }}
                                        </button>
                                    </form>
                                    @if($app->status !== 'rejected')
                                        <form class="inline ml-2" method="POST" action="{{ route('admin.applications.updateStatus', $app) }}">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button class="btn btn-sm">Reject</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center opacity-70 py-6">No applications.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">{{ $apps->withQueryString()->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
