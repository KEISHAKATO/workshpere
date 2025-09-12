<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Jobs</h2></x-slot>

    <div class="max-w-6xl mx-auto p-4">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-2">
                    <input name="q" class="input input-bordered" value="{{ request('q') }}" placeholder="Search title/desc/category">
                    <input name="skills" class="input input-bordered" value="{{ request('skills') }}" placeholder="Skills">
                    <input name="county" class="input input-bordered" value="{{ request('county') }}" placeholder="County">
                    <div class="sm:col-span-3">
                        <button class="btn">Filter</button>
                        <a href="{{ route('public.jobs.index') }}" class="btn btn-ghost ml-2">Reset</a>
                    </div>
                </form>

                <div class="divider my-1"></div>

                <div class="divide-y">
                    @forelse($jobs as $job)
                        <div class="py-4">
                            <a href="{{ route('public.jobs.show', $job) }}" class="link link-primary text-lg font-semibold">{{ $job->title }}</a>
                            <div class="text-sm opacity-70 mt-1">
                                {{ ucfirst(str_replace('_',' ',$job->job_type)) }} • {{ $job->location_city }}, {{ $job->location_county }}
                            </div>
                            @if(is_array($job->required_skills))
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($job->required_skills as $s)
                                        <span class="badge">{{ $s }}</span>
                                    @endforeach
                                </div>
                            @endif>
                        </div>
                    @empty
                        <p class="py-6 opacity-70">No jobs found.</p>
                    @endforelse
                </div>

                <div class="mt-4">{{ $jobs->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
