<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl">My Applications</h2>
                <p class="text-sm text-gray-600">Track the status of your job applications.</p>
            </div>
            <a href="{{ route('seeker.jobs.index') }}" class="px-3 py-2 bg-gray-100 rounded hover:bg-gray-200">
                Browse Jobs
            </a>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto p-6">
        @if (session('status'))
            <div class="mb-4 p-3 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
        @endif

        @if($apps->isEmpty())
            <div class="bg-white rounded-xl shadow p-6 text-gray-600">
                You haven’t applied to any jobs yet.
            </div>
        @else
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Job</th>
                            <th class="px-6 py-3">Location</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3">Applied</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($apps as $app)
                            @php
                                $job = $app->job;
                                $statusBg = $app->status === 'accepted'
                                    ? 'bg-green-600'
                                    : ($app->status === 'rejected' ? 'bg-red-600' : 'bg-gray-600');
                            @endphp
                            <tr class="text-sm">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">
                                        <a class="hover:underline" href="{{ route('public.jobs.show', $job) }}">
                                            {{ $job->title }}
                                        </a>
                                    </div>
                                    @if($job->pay_min || $job->pay_max)
                                        <div class="text-gray-500">
                                            {{ $job->currency ?? 'KES' }}
                                            {{ $job->pay_min ? number_format($job->pay_min) : '—' }} –
                                            {{ $job->pay_max ? number_format($job->pay_max) : '—' }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-gray-700">
                                    {{ $job->location_city ?? '—' }}, {{ $job->location_county ?? '—' }}
                                </td>

                                <td class="px-6 py-4 text-gray-700">
                                    {{ ucfirst(str_replace('_',' ', $job->job_type)) }}
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ optional($app->created_at)->toDayDateTimeString() }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-medium text-white {{ $statusBg }}">
                                        {{ ucfirst($app->status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('public.jobs.show', $job) }}"
                                           class="px-3 py-1.5 rounded bg-blue-600 text-white hover:bg-blue-700">
                                            View Job
                                        </a>

                                        {{-- Seeker -> Employer chat (no seeker_id needed) --}}
                                        <a href="{{ route('chat.show', $job) }}"
                                           class="px-3 py-1.5 rounded bg-gray-100 hover:bg-gray-200">
                                            Chat
                                        </a>

                                        @if($app->status === 'pending')
                                            <form method="POST" action="{{ route('seeker.applications.destroy', $app) }}"
                                                  onsubmit="return confirm('Withdraw this application?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="px-3 py-1.5 rounded bg-gray-600 text-white hover:bg-gray-700">
                                                    Withdraw
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="px-6 py-4 border-t">
                    {{ $apps->links() }}
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
