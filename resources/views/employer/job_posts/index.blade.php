<x-app-layout>
    <div class="max-w-5xl mx-auto p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-semibold">My Job Posts</h1>
            <a href="{{ route('employer.job_posts.create') }}" class="text-blue-600 underline">Create</a>
        </div>
        @if(session('ok')) <div class="p-2 bg-green-100">{{ session('ok') }}</div> @endif
        <ul class="space-y-2">
            @foreach($jobs as $job)
                <li class="border p-3 rounded">
                    <a href="{{ route('employer.job_posts.show',$job) }}" class="font-medium">{{ $job->title }}</a>
                    <div class="text-sm text-gray-600">{{ $job->location_city }}, {{ $job->location_county }} • {{ $job->job_type }}</div>
                </li>
            @endforeach
        </ul>
        <div class="mt-4">{{ $jobs->links() }}</div>
    </div>
</x-app-layout>
