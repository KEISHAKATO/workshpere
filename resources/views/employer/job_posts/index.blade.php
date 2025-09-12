<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl">My Job Posts</h2>
            <a href="{{ route('employer.job_posts.create') }}" class="btn btn-primary">New Job</a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto p-4">
        @if(session('status'))
            <div class="alert alert-success mb-3"><span>{{ session('status') }}</span></div>
        @endif

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobs as $job)
                                <tr>
                                    <td class="font-medium">{{ $job->title }}</td>
                                    <td>
                                        <span class="badge {{ $job->status==='open' ? 'badge-success' : 'badge-ghost' }}">
                                            {{ ucfirst($job->status) }}
                                        </span>
                                    </td>
                                    <td class="opacity-70">{{ $job->created_at->diffForHumans() }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('public.jobs.show', $job) }}" class="link link-primary">View</a>
                                        <a href="{{ route('employer.job_posts.edit', $job) }}" class="link ml-3">Edit</a>
                                        <a href="{{ route('employer.applications.index', $job) }}" class="link ml-3">Applications</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center opacity-70 py-6">No jobs yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">{{ $jobs->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
