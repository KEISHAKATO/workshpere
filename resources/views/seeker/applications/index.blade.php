<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl">My Applications</h2>
                <p class="text-sm opacity-70">Track the status of your job applications.</p>
            </div>
            <a href="{{ route('seeker.jobs.index') }}" class="btn">Browse Jobs</a>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto p-4">


        @if($apps->isEmpty())
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body text-base-content/70">You haven’t applied to any jobs yet.</div>
            </div>
        @else
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body p-0">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Job</th>
                                    <th>Location</th>
                                    <th>Type</th>
                                    <th>Applied</th>
                                    <th>Status</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($apps as $app)
                                    @php
                                        $job = $app->job;
                                        $statusClass = $app->status === 'accepted'
                                            ? 'badge-success'
                                            : ($app->status === 'rejected' ? 'badge-error' : 'badge-ghost');
                                    @endphp
                                    <tr class="text-sm">
                                        <td>
                                            <div class="font-medium">
                                                <a class="link link-primary" href="{{ route('public.jobs.show', $job) }}">
                                                    {{ $job->title }}
                                                </a>
                                            </div>
                                            @if($job->pay_min || $job->pay_max)
                                                <div class="text-xs opacity-70">
                                                    {{ $job->currency ?? 'KES' }}
                                                    {{ $job->pay_min ? number_format($job->pay_min) : '—' }} –
                                                    {{ $job->pay_max ? number_format($job->pay_max) : '—' }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-base-content/80">
                                            {{ $job->location_city ?? '—' }}, {{ $job->location_county ?? '—' }}
                                        </td>
                                        <td class="text-base-content/80">
                                            {{ ucfirst(str_replace('_',' ', $job->job_type)) }}
                                        </td>
                                        <td class="text-base-content/70">
                                            {{ optional($app->created_at)->toDayDateTimeString() }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $statusClass }}">{{ ucfirst($app->status) }}</span>
                                        </td>
                                        <td class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('public.jobs.show', $job) }}" class="btn btn-sm btn-primary">View Job</a>

                                                @php $badge = $unreadByJob[$job->id] ?? 0; @endphp
                                                <a href="{{ route('chat.show', $job) }}" class="btn btn-sm">
                                                    Chat
                                                    @if($badge > 0)
                                                        <span class="badge badge-error ml-2">{{ $badge }}</span>
                                                    @endif
                                                </a>

                                                @if($app->status === 'pending')
                                                    <form method="POST" action="{{ route('seeker.applications.destroy', $app) }}"
                                                          onsubmit="return confirm('Withdraw this application?');">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm">Withdraw</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    @if(in_array($app->status, ['accepted','completed'], true))
                                        <tr class="bg-base-200/60">
                                            <td colspan="6" class="px-6 py-4">
                                                <x-review-form :application="$app" />
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4">{{ $apps->links() }}</div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
