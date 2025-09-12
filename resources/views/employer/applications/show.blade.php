<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl">Applicant Details</h2>
                <p class="text-sm opacity-70">
                    {{ $application->job->title }} • Applied {{ $application->created_at->diffForHumans() }}
                </p>
            </div>
            <a href="{{ route('employer.applications.index', $application->job) }}" class="btn">Back to applications</a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto p-4 space-y-6">
        {{-- Applicant summary --}}
        <section class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $seeker->name }}</h3>
                        <div class="text-sm opacity-70 mt-1">{{ $seeker->email }}</div>
                        @if($profile?->location_city || $profile?->location_county)
                            <div class="text-sm opacity-70 mt-1">
                                {{ $profile->location_city ?? '—' }}, {{ $profile->location_county ?? '—' }}
                            </div>
                        @endif
                    </div>

                    <div class="text-right">
                        <div class="text-xs uppercase opacity-70">Status</div>
                        <div class="mt-1">
                            <span class="badge
                                @class([
                                    'badge-warning' => $application->status === 'pending',
                                    'badge-success' => $application->status === 'accepted',
                                    'badge-error'   => $application->status === 'rejected',
                                ])">
                                {{ ucfirst($application->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Cover letter --}}
        @if($application->cover_letter)
            <section class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h4 class="font-semibold mb-2">Cover Letter</h4>
                    <div class="prose max-w-none whitespace-pre-line">
                        {{ $application->cover_letter }}
                    </div>
                </div>
            </section>
        @endif

        {{-- Seeker profile --}}
        <section class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h4 class="font-semibold mb-4">Seeker Profile</h4>

                @if(!$profile)
                    <p class="opacity-70">This applicant hasn’t filled in their profile yet.</p>
                @else
                    @if(!empty($profile->bio))
                        <div class="mb-4">
                            <div class="text-xs uppercase opacity-70 mb-1">Bio</div>
                            <p>{{ $profile->bio }}</p>
                        </div>
                    @endif

                    @if(!empty($profile->about))
                        <div class="mb-4">
                            <div class="text-xs uppercase opacity-70 mb-1">About</div>
                            <div class="prose max-w-none whitespace-pre-line">
                                {{ $profile->about }}
                            </div>
                        </div>
                    @endif

                    @if(is_array($profile->skills) && count($profile->skills))
                        <div class="mb-4">
                            <div class="text-xs uppercase opacity-70 mb-1">Skills</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($profile->skills as $skill)
                                    <span class="badge">{{ $skill }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="grid sm:grid-cols-3 gap-4 mb-4">
                        <div>
                            <div class="text-xs uppercase opacity-70 mb-1">Experience</div>
                            <div>{{ is_null($profile->experience_years) ? '—' : $profile->experience_years.' yrs' }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase opacity-70 mb-1">City</div>
                            <div>{{ $profile->location_city ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase opacity-70 mb-1">County</div>
                            <div>{{ $profile->location_county ?? '—' }}</div>
                        </div>
                    </div>

                    @if(!is_null($profile->lat) || !is_null($profile->lng))
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs uppercase opacity-70 mb-1">Latitude</div>
                                <div>{{ $profile->lat ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="text-xs uppercase opacity-70 mb-1">Longitude</div>
                                <div>{{ $profile->lng ?? '—' }}</div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </section>

        {{-- Quick actions --}}
        <section class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h4 class="font-semibold mb-3">Actions</h4>
                <form method="POST" action="{{ route('employer.applications.updateStatus', $application) }}" class="flex flex-wrap gap-2">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="accepted" />
                    <button onclick="this.form.status.value='accepted'" class="btn btn-success">
                        Accept
                    </button>
                    <button
                        onclick="event.preventDefault(); this.closest('form').status.value='rejected'; this.closest('form').submit();"
                        class="btn btn-error">
                        Reject
                    </button>
                    <a href="{{ route('employer.applications.index', $application->job) }}" class="btn">
                        Back
                    </a>
                </form>
            </div>
        </section>
    </div>
</x-app-layout>
