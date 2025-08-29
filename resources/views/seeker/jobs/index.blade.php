<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Browse Jobs</h2></x-slot>

    <div class="max-w-6xl mx-auto p-6 bg-white rounded-xl shadow">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
            <input name="q" class="border rounded p-2" value="{{ request('q') }}" placeholder="Search title/desc/category">
            <input name="skills" class="border rounded p-2" value="{{ request('skills') }}" placeholder="Skills: plumbing, tiling">
            <input name="county" class="border rounded p-2" value="{{ request('county') }}" placeholder="County">
            <button class="sm:col-span-3 px-3 py-2 bg-gray-100 rounded">Filter</button>
        </form>

        <div class="divide-y">
            @forelse($jobs as $job)
                <div class="py-4">
                    <a href="{{ route('seeker.jobs.show', $job) }}" class="text-lg font-semibold text-blue-700">{{ $job->title }}</a>
                    <div class="text-sm text-gray-600 mt-1">
                        {{ ucfirst(str_replace('_',' ',$job->job_type)) }} • {{ $job->location_city }}, {{ $job->location_county }}
                    </div>
                    @if(is_array($job->required_skills))
                        <div class="mt-2">
                            @foreach($job->required_skills as $s)
                                <span class="inline-block bg-gray-100 px-2 py-1 rounded text-xs mr-1">{{ $s }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <p class="py-6 text-gray-500">No jobs found.</p>
            @endforelse
        </div>

        <div class="mt-4">{{ $jobs->links() }}</div>
    </div>
</x-app-layout>
