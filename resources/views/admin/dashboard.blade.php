<x-app-layout>
  <x-slot name="header">
    <div>
      <h2 class="font-semibold text-xl text-base-content">Dashboard – Admin</h2>
      <p class="text-sm text-base-content/70">Monitor activity and manage the platform.</p>
    </div>
  </x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-safe sm:px-6 lg:px-8 space-y-8">
      @php
        $userCount = \App\Models\User::count();
        $jobCount  = \App\Models\Job::count();
        $appCount  = \App\Models\Application::count();
      @endphp

      <section class="bg-base-100 shadow rounded-xl p-6 pb-8">
        <h3 class="text-lg font-semibold mb-4">Admin Overview</h3>

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="stat bg-base-200 rounded-xl">
            <div class="stat-title">Users</div>
            <div class="stat-value text-primary">{{ $userCount }}</div>
          </div>
          <div class="stat bg-base-200 rounded-xl">
            <div class="stat-title">Jobs</div>
            <div class="stat-value text-primary">{{ $jobCount }}</div>
          </div>
          <div class="stat bg-base-200 rounded-xl">
            <div class="stat-title">Applications</div>
            <div class="stat-value text-primary">{{ $appCount }}</div>
          </div>
        </div>

        {{-- Actions (mobile-safe, wraps nicely) --}}
        <div class="mt-5 flex flex-wrap gap-3">
          <a href="{{ route('admin.users.index') }}"
             class="btn btn-outline w-full sm:w-auto">
            Manage Users
          </a>

          <a href="{{ route('admin.jobs.index') }}"
             class="btn btn-outline w-full sm:w-auto">
            Manage Jobs
          </a>

          <a href="{{ route('admin.applications.index') }}"
             class="btn btn-outline w-full sm:w-auto">
            Manage Applications
          </a>

          <a href="{{ route('admin.reports.index') }}"
             class="btn btn-primary w-full sm:w-auto">
            Reports
          </a>
        </div>
      </section>
    </div>
  </div>
</x-app-layout>
