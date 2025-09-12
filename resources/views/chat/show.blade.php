<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl">
                Chat - {{ $job->title }}
            </h2>
            <a href="{{ url()->previous() }}" class="btn btn-sm">Back</a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto p-4">
        

        {{-- If employer hasn’t picked a seeker yet --}}
        @if ($isEmployer && empty($otherUserId))
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title">No applicant selected</h3>
                    <p class="text-sm text-base-content/70">
                        Pick an applicant from
                        <a href="{{ route('employer.applications.index', $job) }}" class="link">Applications</a>
                        to start a conversation.
                    </p>
                </div>
            </div>
            @push('scripts') @endpush
            @push('styles') @endpush
            @once @endonce
            @php /** nothing else to render **/ @endphp
        @else
            {{-- Thread --}}
            <div class="card bg-base-100 shadow mb-4">
                <div class="card-body space-y-3 max-h-[60vh] overflow-y-auto">
                    @forelse ($messages as $m)
                        @php $mine = auth()->id() === (int) $m->sender_id; @endphp
                        <div class="chat {{ $mine ? 'chat-end' : 'chat-start' }}">
                            <div class="chat-header text-xs opacity-70">
                                {{ $mine ? 'You' : ($m->sender->name ?? 'User #'.$m->sender_id) }}
                                <time class="ml-2">{{ optional($m->created_at)->toDayDateTimeString() }}</time>
                            </div>
                            <div class="chat-bubble">{{ $m->body }}</div>
                        </div>
                    @empty
                        <p class="text-sm text-base-content/70">No messages yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Composer --}}
            <form
                method="POST"
                action="{{ route('chat.store', $job) }}"
                class="flex gap-2"
            >
                @csrf
                @if($isEmployer)
                    {{-- Employer must include who they’re talking to --}}
                    <input type="hidden" name="seeker_id" value="{{ (int) $otherUserId }}">
                @endif

                <input
                    type="text"
                    name="body"
                    class="input input-bordered flex-1"
                    placeholder="Type your message…"
                    maxlength="5000"
                    required
                />
                <button class="btn btn-primary">Send</button>
            </form>

            @error('body')
            <p class="mt-2 text-error text-sm">{{ $message }}</p>
            @enderror
        @endif
    </div>
</x-app-layout>
