<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Applications</h2>
    </x-slot>

    <div class="max-w-6xl mx-auto p-6">
        @if(session('status')) <div class="mb-4 p-2 bg-green-50 text-green-700 rounded">{{ session('status') }}</div> @endif

        <form method="GET" class="mb-4 flex gap-2">
            <select name="status" class="border rounded px-2 py-1">
                <option value="">All statuses</option>
                <option value="pending"  @selected(($filters['status'] ?? '')==='pending')>Pending</option>
                <option value="accepted" @selected(($filters['status'] ?? '')==='accepted')>Accepted</option>
                <option value="rejected" @selected(($filters['status'] ?? '')==='rejected')>Rejected</option>
            </select>
            <button class="px-3 py-1 rounded bg-gray-100">Filter</button>
        </form>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-2 text-left">Applicant</th>
                        <th class="px-4 py-2">Job</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Applied</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($apps as $app)
                        <tr class="border-t">
                            <td class="px-4 py-2">
                                <div class="font-medium">{{ $app->seeker->name }}</div>
                                <div class="text-xs text-gray-500">{{ $app->seeker->email }}</div>
                            </td>
                            <td class="px-4 py-2">{{ $app->job->title }}</td>
                            <td class="px-4 py-2">{{ ucfirst($app->status) }}</td>
                            <td class="px-4 py-2">{{ $app->created_at->diffForHumans() }}</td>
                            <td class="px-4 py-2">
                                <form class="inline" method="POST" action="{{ route('admin.applications.updateStatus', $app) }}">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="{{ $app->status === 'pending' ? 'accepted' : 'pending' }}">
                                    <button class="px-2 py-1 rounded bg-gray-100">
                                        {{ $app->status === 'pending' ? 'Accept' : 'Set Pending' }}
                                    </button>
                                </form>
                                @if($app->status !== 'rejected')
                                    <form class="inline ml-2" method="POST" action="{{ route('admin.applications.updateStatus', $app) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="rejected">
                                        <button class="px-2 py-1 rounded bg-gray-100">Reject</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if($apps->isEmpty())
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">No applications.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $apps->withQueryString()->links() }}</div>
    </div>
</x-app-layout>
