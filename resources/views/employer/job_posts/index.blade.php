<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl">My Job Posts</h2>
            <a href="{{ route('employer.job_posts.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">New Job</a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto p-6 bg-white rounded-xl shadow">
        @if(session('status')) <div class="mb-3 p-2 bg-green-50 text-green-700 rounded">{{ session('status') }}</div> @endif

        <table class="w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="py-2">Title</th>
                    <th class="py-2">Status</th>
                    <th class="py-2">Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                    <tr class="border-b">
                        <td class="py-2">{{ $job->title }}</td>
                        <td class="py-2 capitalize">{{ $job->status }}</td>
                        <td class="py-2 text-sm text-gray-600">{{ $job->created_at->diffForHumans() }}</td>
                        <td class="py-2 text-right">
                            <a href="{{ route('public.jobs.show', $job) }}" class="text-blue-600 underline">View (public)</a>

                            <a href="{{ route('employer.applications.index', $job) }}" class="ml-3 text-gray-700 underline">
                                Applications
                            </a>
                    </tr>
                @empty
                    <tr><td class="py-4 text-gray-500" colspan="4">No jobs yet.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">{{ $jobs->links() }}</div>
    </div>
</x-app-layout>
