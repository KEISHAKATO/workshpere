<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Admin Dashboard</h2>
    </x-slot>

    <div class="max-w-6xl mx-auto p-6 space-y-6">
        @if(session('status')) <div class="p-2 bg-green-50 text-green-700 rounded">{{ session('status') }}</div> @endif

        <div class="grid sm:grid-cols-3 gap-4">
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

        <div class="grid sm:grid-cols-3 gap-4">
            <div class="border rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $flaggedUsers }}</div>
                <div class="text-gray-600">Flagged Users</div>
            </div>
            <div class="border rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $flaggedJobs }}</div>
                <div class="text-gray-600">Flagged Jobs</div>
            </div>
            <div class="border rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $openJobs }}</div>
                <div class="text-gray-600">Open Jobs</div>
            </div>
        </div>

        <div class="flex gap-3">
            <a class="px-3 py-2 rounded bg-gray-100" href="{{ route('admin.users.index') }}">Manage Users</a>
            <a class="px-3 py-2 rounded bg-gray-100" href="{{ route('admin.jobs.index') }}">Manage Jobs</a>
            <a class="px-3 py-2 rounded bg-gray-100" href="{{ route('admin.applications.index') }}">Manage Applications</a>
        </div>
    </div>
</x-app-layout>
