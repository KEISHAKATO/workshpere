<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Browse Jobs</h2>
    </x-slot>

    <div class="max-w-6xl mx-auto p-6">
        {{-- Filter form --}}
        <form method="GET" class="mb-4 grid grid-cols-1 md:grid-cols-6 gap-3">
            <input name="search" value="{{ request('search') }}" class="border rounded p-2" placeholder="Keyword or skill">
            <input name="county" value="{{ request('county') }}" class="border rounded p-2" placeholder="County">
            <select name="job_type" class="border rounded p-2">
                <option value="">Any type</option>
                @foreach(['full_time'=>'Full time','part_time'=>'Part time','gig'=>'Gig','contract'=>'Contract'] as $val=>$label)
                    <option value="{{ $val }}" @selected(request('job_type')===$val)>{{ $label }}</option>
                @endforeach
            </select>
            <input name="min_pay" type="number" min="0" value="{{ request('min_pay') }}" class="border rounded p-2" placeholder="Min pay">
            <input name="max_pay" type="number" min="0" value="{{ request('max_pay') }}" class="border rounded p-2" placeholder="Max pay">
            <select name="sort" class="border rounded p-2">
                <option value="newest"   @selected(request('sort')==='newest')>Newest</option>
                <option value="oldest"   @selected(request('sort')==='oldest')>Oldest</option>
                <option value="pay_high" @selected(request('sort')==='pay_high')>Highest pay</option>
                <option value="pay_low"  @selected(request('sort')==='pay_low')>Lowest pay</option>
            </select>
            <div class="md:col-span-6 flex gap-2">
                <button class="bg-blue-600 text-white rounded px-4">Filter</button>
                <a href="{{ route('public.jobs.index') }}" class="text-sm text-gray-600 underline">Reset</a>
            </div>
        </form>

        {{-- Job cards --}}
        @forelse($jobs as $job)
            <a href="{{ route('public.jobs.show', $job) }}"
               class="block bg-white rounded-xl shadow p-4 mb-4 hover:bg-gray-50">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $job->title }}</h3>
                        <p class="text-gray-600 mt-1 line-clamp-2">{{ Str::limit($job->description, 160) }}</p>
                        <div class="text-sm text-gray-500 mt-2">
                            {{ $job->location_city ?? '—' }}, {{ $job->location_county ?? '—' }}
                            • {{ ucfirst(str_replace('_',' ', $job->job_type)) }}
                            @if(is_array($job->required_skills))
                                • Skills: {{ implode(', ', $job->required_skills) }}
                            @endif
                        </div>
                    </div>
                    <div class="text-right text-sm text-gray-500">
                        @if($job->pay_min || $job->pay_max)
                            <div class="font-medium text-gray-700">
                                {{ $job->currency ?? 'KES' }}
                                {{ $job->pay_min ? number_format($job->pay_min) : '—' }}
                                –
                                {{ $job->pay_max ? number_format($job->pay_max) : '—' }}
                            </div>
                        @endif
                        <div class="mt-1">
                            Posted {{ optional($job->posted_at)->diffForHumans() ?? 'recently' }}
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-xl shadow p-6 text-gray-600">
                No jobs found. Try changing your filters.
            </div>
        @endforelse

        <div class="mt-6">
            {{ $jobs->links() }}
        </div>
    </div>
</x-app-layout>
