<x-app-layout>
    @php $user = auth()->user(); @endphp

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl">
                Dashboard – {{ ucfirst($user->role) }}
            </h2>
            <p class="text-sm opacity-70 mt-1">
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
        <div class="max-w-7xl mx-auto px-4 space-y-8">
            {{-- Seeker --}}
            @if($user->isSeeker())
                @php
                    $myApplications = \App\Models\Application::where('seeker_id', $user->id)->count();
                    $pendingApps    = \App\Models\Application::where('seeker_id', $user->id)->where('status','pending')->count();
                    $openJobs       = \App\Models\Job::where('status','open')->count();
                @endphp

                <section class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title">For Job Seekers</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="stat bg-base-200 rounded-box">
                                <div class="stat-title">Open Jobs</div>
                                <div class="stat-value text-primary">{{ $openJobs }}</div>
                            </div>
                            <div class="stat bg-base-200 rounded-box">
                                <div class="stat-title">My Applications</div>
                                <div class="stat-value">{{ $myApplications }}</div>
                            </div>
                            <div class="stat bg-base-200 rounded-box">
                                <div class="stat-title">Pending Decisions</div>
                                <div class="stat-value">{{ $pendingApps }}</div>
                            </div>
                        </div>

                        <div class="pt-2 flex gap-3">
                            <a href="{{ route('seeker.jobs.index') }}" class="btn btn-primary">Browse Jobs</a>
                            <a href="{{ route('seeker.profile.edit') }}" class="btn">Edit Profile</a>
                        </div>
                    </div>
                </section>

                {{-- Recommendations --}}
                @if(isset($seekerRecommendations) && $seekerRecommendations->isNotEmpty())
                    <section class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title">Recommended Jobs for You</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($seekerRecommendations as $rec)
                                    @php /** @var \App\Models\Job $job */ $job = $rec['job']; $score = $rec['score']; @endphp
                                    <a href="{{ route('public.jobs.show', $job) }}" class="card bg-base-200 hover:bg-base-300 transition">
                                        <div class="card-body">
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <div class="font-semibold">{{ $job->title }}</div>
                                                    <div class="text-sm opacity-70 mt-1">
                                                        {{ $job->location_city ?? '—' }}, {{ $job->location_county ?? '—' }}
                                                        • {{ ucfirst(str_replace('_',' ', $job->job_type)) }}
                                                    </div>
                                                    @if(is_array($job->required_skills) && count($job->required_skills))
                                                        <div class="mt-2 text-xs opacity-70 line-clamp-1">
                                                            Skills: {{ implode(', ', $job->required_skills) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-xs uppercase opacity-60">Match</div>
                                                    <div class="text-lg font-bold">{{ $score }}%</div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
            @endif

            {{-- Employer --}}
            @if($user->isEmployer())
                @php
                    $myJobs             = \App\Models\Job::where('employer_id',$user->id)->count();
                    $myOpenJobs         = \App\Models\Job::where('employer_id',$user->id)->where('status','open')->count();
                    $incomingApplicants = \App\Models\Application::whereHas('job', fn($q)=>$q->where('employer_id',$user->id))
                        ->where('status','pending')->count();
                @endphp

                <section class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title">For Employers</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="stat bg-base-200 rounded-box">
                                <div class="stat-title">Total Job Posts</div>
                                <div class="stat-value">{{ $myJobs }}</div>
                            </div>
                            <div class="stat bg-base-200 rounded-box">
                                <div class="stat-title">Open Positions</div>
                                <div class="stat-value text-primary">{{ $myOpenJobs }}</div>
                            </div>
                            <div class="stat bg-base-200 rounded-box">
                                <div class="stat-title">Pending Applications</div>
                                <div class="stat-value">{{ $incomingApplicants }}</div>
                            </div>
                        </div>
                        <div class="pt-2 flex gap-3">
                            <a href="{{ route('employer.job_posts.create') }}" class="btn btn-primary">Post a Job</a>
                            <a href="{{ route('employer.job_posts.index') }}" class="btn">Manage Job Posts</a>
                            <a href="{{ route('employer.profile.edit') }}" class="btn">Edit Company Profile</a>
                        </div>
                    </div>
                </section>

                {{-- Suggested Candidates (optional) --}}
                @if(isset($employerSuggestions) && $employerSuggestions->isNotEmpty())
                    <section class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title">Suggested Candidates</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($employerSuggestions as $row)
                                    @php /** @var \App\Models\Profile $p */ $p = $row['profile']; $best = $row['best_score']; @endphp
                                    <div class="card bg-base-200">
                                        <div class="card-body">
                                            <div class="font-semibold">{{ $p->user?->name ?? 'Seeker #'.$p->user_id }}</div>
                                            <div class="text-sm opacity-70">
                                                {{ $p->location_city ?? '—' }}, {{ $p->location_county ?? '—' }}
                                            </div>
                                            @if(is_array($p->skills) && count($p->skills))
                                                <div class="mt-2 text-xs opacity-70 line-clamp-2">
                                                    Skills: {{ implode(', ', array_slice($p->skills, 0, 8)) }}
                                                </div>
                                            @endif
                                            <div class="mt-3 text-right">
                                                <span class="text-xs uppercase opacity-60">Match</span>
                                                <span class="text-lg font-bold ml-1">{{ $best }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
            @endif

            {{-- Admin --}}
            @if($user->isAdmin())
                @php
                    $userCount = \App\Models\User::count();
                    $jobCount  = \App\Models\Job::count();
                    $appCount  = \App\Models\Application::count();
                @endphp

                <section class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title">Admin Overview</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <div class="stat bg-base-200 rounded-box">
                                <div class="stat-title">Users</div>
                                <div class="stat-value">{{ $userCount }}</div>
                            </div>
                            <div class="stat bg-base-200 rounded-box">
                                <div class="stat-title">Jobs</div>
                                <div class="stat-value">{{ $jobCount }}</div>
                            </div>
                            <div class="stat bg-base-200 rounded-box">
                                <div class="stat-title">Applications</div>
                                <div class="stat-value">{{ $appCount }}</div>
                            </div>
                            <a href="{{ route('admin.reports.index') }}" class="card bg-base-200 hover:bg-base-300 transition">
                                <div class="card-body">
                                    <div class="text-3xl">📊</div>
                                    <div class="opacity-70">Reports</div>
                                    <div class="mt-2 text-sm opacity-60">Charts &amp; KPIs</div>
                                </div>
                            </a>
                        </div>
                        <div class="pt-3">
                            <a href="{{ route('admin.users.index') }}" class="btn">Manage Users</a>
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-primary ml-2">Open Reports</a>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
