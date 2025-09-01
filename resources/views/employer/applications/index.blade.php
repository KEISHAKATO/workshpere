<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            Applications for: {{ $job->title }}
        </h2>
    </x-slot>

    <div class="max-w-6xl mx-auto p-6">
        @if(session('status'))
            <div class="mb-4 p-2 bg-green-50 text-green-700 rounded">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-xl overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-2 text-left">Applicant</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Applied</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr class="border-t">
                            <td class="px-4 py-2">
                                <div class="font-medium text-gray-900">
                                    {{ $app->seeker->name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $app->seeker->email }}
                                </div>
                            </td>

                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-xs
                                    @class([
                                        'bg-yellow-100 text-yellow-800' => $app->status === 'pending',
                                        'bg-green-100 text-green-800'   => $app->status === 'accepted',
                                        'bg-red-100 text-red-800'       => $app->status === 'rejected',
                                    ])">
                                    {{ ucfirst($app->status) }}
                                </span>
                            </td>

                            <td class="px-4 py-2">
                                {{ $app->created_at->diffForHumans() }}
                            </td>

                            <td class="px-4 py-2 flex gap-3 items-center">
                                {{-- View full applicant details --}}
                                <a href="{{ route('employer.applications.show', $app) }}"
                                   class="text-blue-600 hover:underline text-sm">
                                    View
                                </a>

                                {{-- Chat with applicant (must pass seeker_id) --}}
                                <a href="{{ route('chat.show', ['job' => $app->job_id, 'seeker_id' => $app->seeker_id]) }}"
                                   class="text-sm px-2 py-1 rounded bg-gray-100 hover:bg-gray-200">
                                    Chat
                                </a>

                                {{-- Quick Accept/Reject --}}
                                <form method="POST" action="{{ route('employer.applications.updateStatus', $app) }}" class="flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="accepted">

                                    <button type="submit"
                                            onclick="this.form.status.value='accepted'"
                                            class="text-green-600 hover:underline text-sm">
                                        Accept
                                    </button>

                                    <button type="submit"
                                            onclick="event.preventDefault(); this.form.status.value='rejected'; this.form.submit();"
                                            class="text-red-600 hover:underline text-sm">
                                        Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                No applications yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
