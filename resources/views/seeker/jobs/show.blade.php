<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl truncate">{{ $job->title }}</h2>

            <div class="flex gap-2">
                <a href="{{ route('public.jobs.show', $job) }}" class="btn btn-sm">View public</a>
                @if($isOwner)
                    <a href="{{ route('employer.job_posts.edit', $job) }}" class="btn btn-sm">Edit (owner)</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto p-4 grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: job details --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="card bg-base-100 shadow">
                <div class="card-body space-y-4 text-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                        <div>
                            <div class="font-semibold">Required skills</div>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($job->required_skills as $s)
                                    <span class="badge">{{ $s }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <h3 class="font-semibold text-lg">Description</h3>
                        <p class="opacity-90 whitespace-pre-line">{{ $job->description }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: application panel --}}
        <div class="space-y-4">
            @if($job->status !== 'open')
                <div class="alert alert-info"><span>This job is not open for applications.</span></div>
            @elseif($isOwner)
                <div class="alert alert-warning"><span>You posted this job — seekers can apply from their accounts.</span></div>
            @elseif($hasApplied)
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="alert alert-success mb-3"><span>You already applied to this job.</span></div>
                        <a href="{{ route('seeker.applications.index') }}" class="btn btn-block">View my applications</a>
                    </div>
                </div>
            @else
                {{-- Apply form --}}
                <div class="card bg-base-100 shadow">
                    <div class="card-body space-y-3">
                        <h3 class="font-semibold text-lg">Apply</h3>

                        <form method="POST" action="{{ route('seeker.apply.store', $job) }}" class="space-y-3">
                            @csrf

                            <label class="label">
                                <span class="label-text">Cover letter (optional)</span>
                            </label>
                            <textarea
                                name="cover_letter"
                                rows="6"
                                class="textarea textarea-bordered w-full"
                                placeholder="Briefly explain why you’re a good fit…">{{ old('cover_letter') }}</textarea>
                            <x-input-error :messages="$errors->get('cover_letter')" class="mt-1" />

                            <button type="submit" class="btn btn-primary w-full">Submit application</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
