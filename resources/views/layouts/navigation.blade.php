{{-- resources/views/layouts/navigation.blade.php --}}
<nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ url('/') }}" class="text-lg font-semibold">WORKSPHERE</a>

                <a href="{{ route('public.jobs.index') }}" class="text-gray-700 hover:text-gray-900">Jobs</a>

                @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900">Dashboard</a>

                    {{-- Seeker-only --}}
                    @if(auth()->user()->isSeeker())
                        <a href="{{ route('seeker.jobs.index') }}" class="text-gray-700 hover:text-gray-900">Browse Jobs</a>
                        <a href="{{ route('seeker.profile.edit') }}" class="text-gray-700 hover:text-gray-900">My Profile</a>
                        <a href="{{ route('seeker.applications.index') }}" class="text-gray-700 hover:text-gray-900">My Applications</a>
                    @endif

                    {{-- Employer-only --}}
                    @if(auth()->user()->isEmployer())
                        <a href="{{ route('employer.job_posts.index') }}" class="text-gray-700 hover:text-gray-900">My Job Posts</a>
                        <a href="{{ route('employer.job_posts.create') }}" class="text-gray-700 hover:text-gray-900">Post a Job</a>
                        <a href="{{ route('employer.profile.edit') }}" class="text-gray-700 hover:text-gray-900">Company Profile</a>
                    @endif

                    {{-- Admin-only --}}
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.users.index') }}" class="text-gray-700 hover:text-gray-900">Users</a>
                        <a href="{{ route('admin.jobs.index') }}" class="text-gray-700 hover:text-gray-900">Jobs</a>
                        <a href="{{ route('admin.applications.index') }}" class="text-gray-700 hover:text-gray-900">Applications</a>
                        <a href="{{ route('admin.reports.index') }}" class="text-gray-700 hover:text-gray-900">Reports</a> {{-- ✅ fixed --}}
                    @endif
                @endauth
            </div>

            <div class="flex items-center gap-4">
                @guest
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Log in</a>
                    <a href="{{ route('register') }}" class="text-gray-700 hover:text-gray-900">Register</a>
                    <a href="#" onclick="if(window.BotManWidget){ BotManWidget.open(); } return false;">
                    Chat Support
                    </a>

                @endguest

                @auth
                    <span class="hidden sm:inline text-sm text-gray-600">
                        {{ auth()->user()->name }} • {{ ucfirst(auth()->user()->role) }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-red-600 hover:text-red-700">Log out</button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>
