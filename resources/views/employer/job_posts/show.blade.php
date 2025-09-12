<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl">{{ $job->title }}</h2>
            <div class="flex items-center gap-2">
                <a class="btn" href="{{ route('employer.job_posts.edit', $job) }}">Edit</a>
                <a class="btn" href="{{ route('public.jobs.show', $job) }}">View public</a>
                <a class="btn btn-primary" href="{{ route('employer.applications.index', $job) }}">Applications</a>
                <a class="btn" href="{{ route('chat.show', $job) }}">Open chat</a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto p-4">

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><span class="opacity-70">Type:</span> <span class="font-medium">{{ ucfirst(str_replace('_',' ', $job->job_type)) }}</span></div>
                    <div><span class="opacity-70">Status:</span>
                        <span class="badge {{ $job->status==='open' ? 'badge-success' : 'badge-ghost' }}">{{ ucfirst($job->status) }}</span>
                    </div>
                    <div><span class="opacity-70">City:</span> <span class="font-medium">{{ $job->location_city ?? '—' }}</span></div>
                    <div><span class="opacity-70">County:</span> <span class="font-medium">{{ $job->location_county ?? '—' }}</span></div>
                    <div class="sm:col-span-2">
                        <span class="opacity-70">Pay:</span>
                        <span class="font-medium">{{ $job->currency ?? 'KES' }} {{ $job->pay_min ? number_format($job->pay_min) : '—' }} – {{ $job->pay_max ? number_format($job->pay_max) : '—' }}</span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="opacity-70">Posted:</span> <span class="font-medium">{{ optional($job->posted_at)->toDayDateTimeString() ?? '—' }}</span>
                    </div>
                </div>

                @if(is_array($job->required_skills) && count($job->required_skills))
                    <div class="text-sm">
                        <div class="font-semibold">Required skills</div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($job->required_skills as $s)
                                <span class="badge">{{ $s }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <h3 class="font-semibold text-lg mt-2">Description</h3>
                    <p class="opacity-90 whitespace-pre-line">{{ $job->description }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
