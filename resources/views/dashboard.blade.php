{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    @php
        $user = auth()->user();
    @endphp

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard – {{ ucfirst($user->role) }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                @switch($user->role)
                    @case('seeker')  Find and apply for jobs that match your skills. @break
                    @case('employer') Post jobs and manage applicants in one place. @break
                    @case('admin')    Monitor activity and manage the platform. @break
                    @default          Welcome back!
                @endswitch
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Seeker section --}}
            @if($user->isSeeker() || $user->isAdmin())
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
            @endif

            {{-- Employer section --}}
            @if($user->isEmployer() || $user->isAdmin())
                @php
                    $myJobs            = \App\Models\Job::where('employer_id',$user->id)->count();
                    $myOpenJobs        = \App\Models\Job::where('employer_id',$user->id)->where('status','open')->count();
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
            @endif

            {{-- Admin section --}}
            @if($user->isAdmin())
                @php
                    $userCount   = \App\Models\User::count();
                    $jobCount    = \App\Models\Job::count();
                    $appCount    = \App\Models\Application::count();
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

                    <div class="mt-5">
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-lg bg-gray-100">Manage Users</a>
                    </div>
                </section>
            @endif

        </div>
    </div>
</x-app-layout>
