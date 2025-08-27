<x-app-layout>
    <div class="max-w-5xl mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-4">Browse Jobs</h1>
        <form class="mb-4 flex gap-2">
            <input class="border p-2 flex-1" type="text" name="q" value="{{ request('q') }}" placeholder="Search title/description">
            <input class="border p-2" type="text" name="county" value="{{ request('county') }}" placeholder="County">
            <select class="border p-2" name="type">
                <option value="">Any type</option>
                @foreach(['full_time','part_time','gig','contract'] as $t)
                    <option value="{{ $t }}" @selected(request('type')===$t)>{{ str_replace('_',' ', $t) }}</option>
                @endforeach
            </select>
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
        </form>

        <ul class="space-y-3">
            @foreach($jobs as $job)
                <li class="border p-3 rounded">
                    <a class="font-medium" href="{{ route('seeker.jobs.show', $job) }}">{{ $job->title }}</a>
                    <div class="text-sm text-gray-600">{{ $job->location_city }}, {{ $job->location_county }} • {{ $job->job_type }}</div>
                </li>
            @endforeach
        </ul>
        <div class="mt-4">{{ $jobs->links() }}</div>
    </div>
</x-app-layout>
