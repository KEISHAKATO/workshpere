{{-- resources/views/layouts/sidebar.blade.php --}}
@php
    $user = auth()->user();
    function active($pattern) {
        return request()->routeIs($pattern) ? 'active' : '';
    }
@endphp

<nav class="menu p-4 w-full">
    <ul class="menu-lg">
        <li class="menu-title">General</li>
        <li>
            <a href="{{ route('dashboard') }}" class="{{ active('dashboard') }}">
                <span class="material-symbols-outlined text-base-content/70">dashboard</span>
                <span>Dashboard</span>
            </a>
        </li>

        {{-- Seeker --}}
        @if($user?->isSeeker())
            <li class="menu-title mt-4">For Seekers</li>
            <li>
                <a href="{{ route('seeker.jobs.index') }}" class="{{ active('seeker.jobs.*') }}">
                    Browse Jobs
                </a>
            </li>
            <li>
                <a href="{{ route('seeker.applications.index') }}" class="{{ active('seeker.applications.*') }}">
                    My Applications
                </a>
            </li>
            <li>
                <a href="{{ route('seeker.profile.edit') }}" class="{{ active('seeker.profile.edit') }}">
                    My Profile
                </a>
            </li>
        @endif

        {{-- Employer --}}
        @if($user?->isEmployer())
            <li class="menu-title mt-4">For Employers</li>
            <li>
                <a href="{{ route('employer.job_posts.index') }}" class="{{ active('employer.job_posts.*') }}">
                    Job Posts
                </a>
            </li>
            <li>
                <a href="{{ route('employer.job_posts.create') }}" class="{{ active('employer.job_posts.create') }}">
                    Post a Job
                </a>
            </li>
            <li>
                <a href="{{ route('employer.profile.edit') }}" class="{{ active('employer.profile.edit') }}">
                    Company Profile
                </a>
            </li>
        @endif

        {{-- Admin --}}
        @if($user?->isAdmin())
            <li class="menu-title mt-4">Admin</li>
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ active('admin.dashboard') }}">
                    Overview
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users.index') }}" class="{{ active('admin.users.*') }}">
                    Users
                </a>
            </li>
            <li>
                <a href="{{ route('admin.jobs.index') }}" class="{{ active('admin.jobs.*') }}">
                    Jobs
                </a>
            </li>
            <li>
                <a href="{{ route('admin.applications.index') }}" class="{{ active('admin.applications.*') }}">
                    Applications
                </a>
            </li>
            <li>
                <a href="{{ route('admin.reports.index') }}" class="{{ active('admin.reports.*') }}">
                    Reports
                </a>
            </li>
        @endif
    </ul>
</nav>
