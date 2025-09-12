<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Jobs</h2></x-slot>

    <div class="max-w-6xl mx-auto p-4">
        @if(session('status')) <div class="alert alert-success mb-4"><span>{{ session('status') }}</span></div> @endif

        <form method="GET" class="mb-4 flex flex-wrap gap-3 items-end">
            <div class="form-control">
                <label class="label"><span class="label-text">Status</span></label>
                <select name="status" class="select select-bordered">
                    <option value="">All statuses</option>
                    <option value="open"   @selected(($filters['status'] ?? '')==='open')>Open</option>
                    <option value="closed" @selected(($filters['status'] ?? '')==='closed')>Closed</option>
                </select>
            </div>
            <label class="label cursor-pointer gap-2">
                <span class="label-text">Flagged only</span>
                <input type="checkbox" name="flagged" value="1" class="checkbox"
                       @checked(($filters['flagged'] ?? false)) />
            </label>
            <button class="btn">Filter</button>
        </form>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table text-sm">
                        <thead>
                        <tr>
                            <th>Title</th>
                            <th>Employer</th>
                            <th>Status</th>
                            <th>Flags</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($jobs as $job)
                            <tr>
                                <td>
                                    <div class="font-medium">{{ $job->title }}</div>
                                    <div class="text-xs opacity-70">{{ $job->category }} • {{ ucfirst($job->job_type) }}</div>
                                </td>
                                <td>
                                    <div class="text-sm">{{ $job->employer->name ?? '—' }}</div>
                                    <div class="text-xs opacity-70">{{ $job->employer->email ?? '' }}</div>
                                </td>
                                <td>{{ ucfirst($job->status) }}</td>
                                <td>
                                    @if($job->is_flagged)
                                        <span class="badge badge-warning">Flagged</span>
                                    @else
                                        <span class="opacity-60 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <form class="inline" method="POST" action="{{ route('admin.jobs.setStatus', $job) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="{{ $job->status === 'open' ? 'closed' : 'open' }}"/>
                                        <button class="btn btn-sm">
                                            {{ $job->status === 'open' ? 'Close' : 'Reopen' }}
                                        </button>
                                    </form>
                                    <form class="inline ml-2" method="POST" action="{{ route('admin.jobs.toggleFlag', $job) }}">
                                        @csrf @method('PUT')
                                        <button class="btn btn-sm">{{ $job->is_flagged ? 'Unflag' : 'Flag' }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center opacity-70 py-6">No jobs.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">{{ $jobs->withQueryString()->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
