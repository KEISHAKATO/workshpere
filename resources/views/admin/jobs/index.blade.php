<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Jobs</h2>
    </x-slot>

    <div class="max-w-6xl mx-auto p-6">
        @if(session('status')) <div class="mb-4 p-2 bg-green-50 text-green-700 rounded">{{ session('status') }}</div> @endif

        <form method="GET" class="mb-4 flex gap-2">
            <select name="status" class="border rounded px-2 py-1">
                <option value="">All statuses</option>
                <option value="open"   @selected(($filters['status'] ?? '')==='open')>Open</option>
                <option value="closed" @selected(($filters['status'] ?? '')==='closed')>Closed</option>
            </select>
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="flagged" value="1" @checked(($filters['flagged'] ?? false)) />
                <span>Flagged only</span>
            </label>
            <button class="px-3 py-1 rounded bg-gray-100">Filter</button>
        </form>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-2 text-left">Title</th>
                        <th class="px-4 py-2">Employer</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Flags</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jobs as $job)
                        <tr class="border-t">
                            <td class="px-4 py-2">
                                <div class="font-medium">{{ $job->title }}</div>
                                <div class="text-xs text-gray-500">{{ $job->category }} • {{ ucfirst($job->job_type) }}</div>
                            </td>
                            <td class="px-4 py-2">
                                <div class="text-sm">{{ $job->employer->name ?? '—' }}</div>
                                <div class="text-xs text-gray-500">{{ $job->employer->email ?? '' }}</div>
                            </td>
                            <td class="px-4 py-2">{{ ucfirst($job->status) }}</td>
                            <td class="px-4 py-2">
                                @if($job->is_flagged)
                                    <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Flagged</span>
                                @else
                                    <span class="text-xs text-gray-500">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                <form class="inline" method="POST" action="{{ route('admin.jobs.setStatus', $job) }}">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="{{ $job->status === 'open' ? 'closed' : 'open' }}"/>
                                    <button class="px-2 py-1 rounded bg-gray-100">
                                        {{ $job->status === 'open' ? 'Close' : 'Reopen' }}
                                    </button>
                                </form>

                                <form class="inline ml-2" method="POST" action="{{ route('admin.jobs.toggleFlag', $job) }}">
                                    @csrf @method('PUT')
                                    <button class="px-2 py-1 rounded bg-gray-100">
                                        {{ $job->is_flagged ? 'Unflag' : 'Flag' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if($jobs->isEmpty())
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">No jobs.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $jobs->withQueryString()->links() }}</div>
    </div>
</x-app-layout>
