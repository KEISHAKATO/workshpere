<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl">
                    Applicant Details
                </h2>
                <p class="text-sm text-gray-600">
                    {{ $application->job->title }} • Applied {{ $application->created_at->diffForHumans() }}
                </p>
            </div>
            <a href="{{ route('employer.applications.index', $application->job) }}"
               class="text-sm px-3 py-2 rounded bg-gray-100 hover:bg-gray-200">
                Back to applications
            </a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto p-6 space-y-6">

        {{-- Applicant summary --}}
        <section class="bg-white shadow rounded-xl p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold">
                        {{ $seeker->name }}
                    </h3>
                    <div class="text-sm text-gray-600 mt-1">{{ $seeker->email }}</div>
                    @if($profile?->location_city || $profile?->location_county)
                        <div class="text-sm text-gray-500 mt-1">
                            {{ $profile->location_city ?? '—' }}, {{ $profile->location_county ?? '—' }}
                        </div>
                    @endif
                </div>

                {{-- Current application status --}}
                <div class="text-right">
                    <div class="text-xs uppercase text-gray-500">Status</div>
                    <div class="mt-1 inline-flex items-center rounded px-2 py-1
                        @class([
                            'bg-yellow-100 text-yellow-800' => $application->status === 'pending',
                            'bg-green-100 text-green-800' => $application->status === 'accepted',
                            'bg-red-100 text-red-800' => $application->status === 'rejected',
                        ])">
                        {{ ucfirst($application->status) }}
                    </div>
                </div>
            </div>
        </section>

        {{-- Cover letter --}}
        @if($application->cover_letter)
            <section class="bg-white shadow rounded-xl p-6">
                <h4 class="font-semibold mb-2">Cover Letter</h4>
                <div class="prose max-w-none whitespace-pre-line">
                    {{ $application->cover_letter }}
                </div>
            </section>
        @endif

        {{-- Seeker profile details --}}
        <section class="bg-white shadow rounded-xl p-6">
            <h4 class="font-semibold mb-4">Seeker Profile</h4>

            @if(!$profile)
                <p class="text-gray-600">This applicant hasn’t filled in their profile yet.</p>
            @else
                @if(!empty($profile->bio))
                    <div class="mb-4">
                        <div class="text-xs uppercase text-gray-500 mb-1">Bio</div>
                        <p class="text-gray-800">{{ $profile->bio }}</p>
                    </div>
                @endif

                @if(!empty($profile->about))
                    <div class="mb-4">
                        <div class="text-xs uppercase text-gray-500 mb-1">About</div>
                        <div class="prose max-w-none text-gray-800 whitespace-pre-line">
                            {{ $profile->about }}
                        </div>
                    </div>
                @endif

                @if(is_array($profile->skills) && count($profile->skills))
                    <div class="mb-4">
                        <div class="text-xs uppercase text-gray-500 mb-1">Skills</div>
                        <div>
                            @foreach($profile->skills as $skill)
                                <span class="inline-block bg-gray-100 rounded px-2 py-1 text-xs mr-1 mt-1">
                                    {{ $skill }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="grid sm:grid-cols-3 gap-4 mb-4">
                    <div>
                        <div class="text-xs uppercase text-gray-500 mb-1">Experience</div>
                        <div class="text-gray-800">
                            {{ is_null($profile->experience_years) ? '—' : $profile->experience_years.' yrs' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs uppercase text-gray-500 mb-1">City</div>
                        <div class="text-gray-800">{{ $profile->location_city ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase text-gray-500 mb-1">County</div>
                        <div class="text-gray-800">{{ $profile->location_county ?? '—' }}</div>
                    </div>
                </div>

                @if(!is_null($profile->lat) || !is_null($profile->lng))
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs uppercase text-gray-500 mb-1">Latitude</div>
                            <div class="text-gray-800">{{ $profile->lat ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase text-gray-500 mb-1">Longitude</div>
                            <div class="text-gray-800">{{ $profile->lng ?? '—' }}</div>
                        </div>
                    </div>
                @endif
            @endif
        </section>

        {{-- Quick actions --}}
        <section class="bg-white shadow rounded-xl p-6">
            <h4 class="font-semibold mb-3">Actions</h4>
            <form method="POST" action="{{ route('employer.applications.updateStatus', $application) }}" class="flex flex-wrap gap-2">
                @csrf
                @method('PUT')

                <input type="hidden" name="status" value="accepted" />
                <button
                    onclick="this.form.status.value='accepted'"
                    class="px-3 py-2 rounded bg-green-600 text-white hover:bg-green-700">
                    Accept
                </button>

                <button
                    onclick="event.preventDefault(); this.closest('form').status.value='rejected'; this.closest('form').submit();"
                    class="px-3 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                    Reject
                </button>

                <a href="{{ route('employer.applications.index', $application->job) }}"
                   class="px-3 py-2 rounded bg-gray-100 hover:bg-gray-200">
                    Back
                </a>
            </form>
        </section>
    </div>
</x-app-layout>
