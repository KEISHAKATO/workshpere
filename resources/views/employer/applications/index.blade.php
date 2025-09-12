<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            Applications for: {{ $job->title }}
        </h2>
    </x-slot>

    <div class="max-w-6xl mx-auto p-4">


        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table text-sm">
                        <thead>
                            <tr>
                                <th>Applicant</th>
                                <th>Status</th>
                                <th>Applied</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                @php
                                    $unread = (int) ($unreadBySeeker[$app->seeker_id] ?? 0);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div>
                                                <div class="font-medium">{{ $app->seeker->name }}</div>
                                                <div class="text-xs opacity-70">{{ $app->seeker->email }}</div>
                                            </div>
                                            @if($unread > 0)
                                                <span class="badge badge-primary">{{ $unread }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge
                                            @class([
                                                'badge-warning' => $app->status === 'pending',
                                                'badge-success' => $app->status === 'accepted',
                                                'badge-error'   => $app->status === 'rejected',
                                            ])">
                                            {{ ucfirst($app->status) }}
                                        </span>
                                    </td>
                                    <td class="opacity-70">
                                        {{ $app->created_at->diffForHumans() }}
                                    </td>
                                    <td class="text-right">
                                        <div class="flex gap-2 items-center justify-end">
                                            <a href="{{ route('employer.applications.show', $app) }}"
                                               class="link link-primary text-sm">
                                                View
                                            </a>

                                            <a href="{{ route('chat.show', [$app->job, 'seeker_id' => $app->seeker_id]) }}"
                                               class="btn btn-xs">
                                                Chat
                                                @if($unread > 0)
                                                    <span class="badge badge-primary ml-2">{{ $unread }}</span>
                                                @endif
                                            </a>

                                            <form method="POST" action="{{ route('employer.applications.updateStatus', $app) }}" class="flex gap-2">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="accepted">
                                                <button type="submit" onclick="this.form.status.value='accepted'" class="link text-success text-sm">
                                                    Accept
                                                </button>
                                                <button type="submit"
                                                        onclick="event.preventDefault(); this.form.status.value='rejected'; this.form.submit();"
                                                        class="link text-error text-sm">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                @if(in_array($app->status, ['accepted','completed'], true))
                                    <tr class="bg-base-200/60">
                                        <td colspan="4" class="px-4 py-3">
                                            <x-review-form :application="$app" perspective="employer" />
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center opacity-70 py-6">
                                        No applications yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">{{ $applications->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
