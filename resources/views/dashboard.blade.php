{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    @php $user = auth()->user(); @endphp

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard – {{ ucfirst($user->role) }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                @if($user->isSeeker())
                    Find and apply for jobs that match your skills.
                @elseif($user->isEmployer())
                    Post jobs and manage applicants in one place.
                @elseif($user->isAdmin())
                    Monitor activity and manage the platform.
                @else
                    Welcome back!
                @endif
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Seeker section (ONLY seekers) --}}
            @if($user->isSeeker())
                @php
                    $myApplications = \App\Models\Application::where('seeker_id', $user->id)->count();
                    $pendingApps    = \App\Models\Application::where('seeker_id', $user->id)->where('status','pending')->count();
                    $openJobs       = \App\Models\Job::where('status','open')->count();
                @endphp

                <section class="bg-white shadow rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4">For Job Seekers</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="border rounded-lg p-4">
                            <div class="text-3xl font-bold">{{ $openJobs }}</div>
                            <div class="text-gray-600">Open Jobs</div>
                        </div>
                        <div class="border rounded-lg p-4">
                            <div class="text-3xl font-bold">{{ $myApplications }}</div>
                            <div class="text-gray-600">My Applications</div>
                        </div>
                        <div class="border rounded-lg p-4">
                            <div class="text-3xl font-bold">{{ $pendingApps }}</div>
                            <div class="text-gray-600">Pending Decisions</div>
                        </div>
                    </div>

                    <div class="mt-5 flex gap-3">
                        <a href="{{ route('seeker.jobs.index') }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white">Browse Jobs</a>
                        <a href="{{ route('seeker.profile.edit') }}" class="px-4 py-2 rounded-lg bg-gray-100">Edit Profile</a>
                    </div>
                </section>

                {{-- Recommended Jobs (if provided by controller) --}}
                @if(isset($seekerRecommendations) && $seekerRecommendations->isNotEmpty())
                    <section class="bg-white shadow rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4">Recommended Jobs for You</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($seekerRecommendations as $rec)
                                @php /** @var \App\Models\Job $job */ $job = $rec['job']; $score = $rec['score']; @endphp
                                <a href="{{ route('public.jobs.show', $job) }}" class="block border rounded-xl p-4 hover:bg-gray-50">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <div class="font-semibold">{{ $job->title }}</div>
                                            <div class="text-sm text-gray-600 mt-1">
                                                {{ $job->location_city ?? '—' }}, {{ $job->location_county ?? '—' }}
                                                • {{ ucfirst(str_replace('_',' ', $job->job_type)) }}
                                            </div>
                                            @if(is_array($job->required_skills) && count($job->required_skills))
                                                <div class="mt-2 text-xs text-gray-500 line-clamp-1">
                                                    Skills: {{ implode(', ', $job->required_skills) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs uppercase text-gray-500">Match</div>
                                            <div class="text-lg font-bold">{{ $score }}%</div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
            @endif

            {{-- Employer section (ONLY employers) --}}
            @if($user->isEmployer())
                @php
                    $myJobs             = \App\Models\Job::where('employer_id',$user->id)->count();
                    $myOpenJobs         = \App\Models\Job::where('employer_id',$user->id)->where('status','open')->count();
                    $incomingApplicants = \App\Models\Application::whereHas('job', fn($q)=>$q->where('employer_id',$user->id))
                        ->where('status','pending')->count();
                @endphp

                <section class="bg-white shadow rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4">For Employers</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="border rounded-lg p-4">
                            <div class="text-3xl font-bold">{{ $myJobs }}</div>
                            <div class="text-gray-600">Total Job Posts</div>
                        </div>
                        <div class="border rounded-lg p-4">
                            <div class="text-3xl font-bold">{{ $myOpenJobs }}</div>
                            <div class="text-gray-600">Open Positions</div>
                        </div>
                        <div class="border rounded-lg p-4">
                            <div class="text-3xl font-bold">{{ $incomingApplicants }}</div>
                            <div class="text-gray-600">Pending Applications</div>
                        </div>
                    </div>

                    <div class="mt-5 flex gap-3">
                        <a href="{{ route('employer.job_posts.create') }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white">Post a Job</a>
                        <a href="{{ route('employer.job_posts.index') }}" class="px-4 py-2 rounded-lg bg-gray-100">Manage Job Posts</a>
                        <a href="{{ route('employer.profile.edit') }}" class="px-4 py-2 rounded-lg bg-gray-100">Edit Company Profile</a>
                    </div>
                </section>

                {{-- Suggested Candidates (if provided by controller) --}}
                @if(isset($employerSuggestions) && $employerSuggestions->isNotEmpty())
                    <section class="bg-white shadow rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4">Suggested Candidates</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($employerSuggestions as $row)
                                @php /** @var \App\Models\Profile $p */ $p = $row['profile']; $best = $row['best_score']; @endphp
                                <div class="border rounded-xl p-4">
                                    <div class="font-semibold">{{ $p->user?->name ?? 'Seeker #'.$p->user_id }}</div>
                                    <div class="text-sm text-gray-600">
                                        {{ $p->location_city ?? '—' }}, {{ $p->location_county ?? '—' }}
                                    </div>
                                    @if(is_array($p->skills) && count($p->skills))
                                        <div class="mt-2 text-xs text-gray-500 line-clamp-2">
                                            Skills: {{ implode(', ', array_slice($p->skills, 0, 8)) }}
                                        </div>
                                    @endif
                                    <div class="mt-3 text-right">
                                        <span class="text-xs uppercase text-gray-500">Match</span>
                                        <span class="text-lg font-bold ml-1">{{ $best }}%</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            @endif

            {{-- Admin section (ONLY admins) --}}
            @if($user->isAdmin())
                @php
                    $userCount = \App\Models\User::count();
                    $jobCount  = \App\Models\Job::count();
                    $appCount  = \App\Models\Application::count();
                @endphp

                <section class="bg-white shadow rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4">Admin Overview</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="border rounded-lg p-4">
                            <div class="text-3xl font-bold">{{ $userCount }}</div>
                            <div class="text-gray-600">Users</div>
                        </div>
                        <div class="border rounded-lg p-4">
                            <div class="text-3xl font-bold">{{ $jobCount }}</div>
                            <div class="text-gray-600">Jobs</div>
                        </div>
                        <div class="border rounded-lg p-4">
                            <div class="text-3xl font-bold">{{ $appCount }}</div>
                            <div class="text-gray-600">Applications</div>
                        </div>
                    </div>

                    <div class="mt-5 flex gap-3">
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-lg bg-gray-100">Manage Users</a>
                        <a href="{{ route('admin.jobs.index') }}" class="px-4 py-2 rounded-lg bg-gray-100">Manage Jobs</a>
                        <a href="{{ route('admin.applications.index') }}" class="px-4 py-2 rounded-lg bg-gray-100">Manage Applications</a>
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
