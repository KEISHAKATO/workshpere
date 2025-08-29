<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl">
                    Applications — {{ $job->title }}
                </h2>
                <p class="text-sm text-gray-600">Review and act on applications for this job.</p>
            </div>

            <a href="{{ route('employer.job_posts.show', $job) }}"
               class="px-3 py-2 bg-gray-100 rounded hover:bg-gray-200">
                Back to job
            </a>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto p-6">
        @if (session('status'))
            <div class="mb-4 p-3 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
        @endif

        @if($applications->isEmpty())
            <div class="bg-white rounded-xl shadow p-6 text-gray-600">
                No applications yet.
            </div>
        @else
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Applicant</th>
                            <th class="px-6 py-3">Submitted</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Cover letter</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($applications as $app)
                            @php
                                // Choose exactly ONE background class (avoid Tailwind IntelliSense bg-* conflicts)
                                $statusBg = $app->status === 'accepted'
                                    ? 'bg-green-600'
                                    : ($app->status === 'rejected'
                                        ? 'bg-red-600'
                                        : 'bg-gray-600');
                            @endphp
                            <tr class="text-sm">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $app->seeker->name ?? 'Applicant #'.$app->seeker_id }}</div>
                                    <div class="text-gray-500">{{ $app->seeker->email ?? '' }}</div>
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ optional($app->created_at)->toDayDateTimeString() }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-medium text-white {{ $statusBg }}">
                                        {{ ucfirst($app->status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 max-w-md">
                                    <p class="text-gray-700 line-clamp-3 whitespace-pre-line">
                                        {{ $app->cover_letter ?: '—' }}
                                    </p>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">

                                        {{-- Accept --}}
                                        <form method="POST"
                                              action="{{ route('employer.applications.updateStatus', $app) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="accepted">
                                            <button
                                                class="px-3 py-1.5 rounded text-white bg-green-600 hover:bg-green-700">
                                                Accept
                                            </button>
                                        </form>

                                        {{-- Reject --}}
                                        <form method="POST"
                                              action="{{ route('employer.applications.updateStatus', $app) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button
                                                class="px-3 py-1.5 rounded text-white bg-red-600 hover:bg-red-700">
                                                Reject
                                            </button>
                                        </form>

                                        {{-- Mark Pending --}}
                                        <form method="POST"
                                              action="{{ route('employer.applications.updateStatus', $app) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="pending">
                                            <button
                                                class="px-3 py-1.5 rounded text-white bg-gray-600 hover:bg-gray-700">
                                                Pending
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t">
                    {{ $applications->links() }}
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
