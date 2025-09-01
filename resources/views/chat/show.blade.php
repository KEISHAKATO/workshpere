<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl">Chat • {{ $job->title }}</h2>
                @if($isEmployer && $otherUserId)
                    <p class="text-sm text-gray-600">Chatting with seeker #{{ $otherUserId }}</p>
                @endif
            </div>
            <a href="{{ url()->previous() }}" class="text-sm px-3 py-2 rounded bg-gray-100 hover:bg-gray-200">Back</a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto p-6 space-y-4">
        @if(session('status'))
            <div class="p-2 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow p-4 max-h-[60vh] overflow-y-auto">
            @forelse($messages as $m)
                <div class="mb-3">
                    <div class="text-xs text-gray-500">
                        {{ $m->created_at->toDayDateTimeString() }} •
                        @if($m->sender_id === auth()->id()) You @else User #{{ $m->sender_id }} @endif
                        → User #{{ $m->receiver_id }}
                    </div>
                    <div class="mt-1 p-2 rounded {{ $m->sender_id === auth()->id() ? 'bg-blue-50' : 'bg-gray-50' }}">
                        {{ $m->body }}
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No messages yet.</p>
            @endforelse
        </div>

        <form method="POST" action="{{ route('chat.store', $job) }}" class="bg-white rounded-xl shadow p-4 flex gap-2">
            @csrf
            @if($isEmployer)
                {{-- Needed so employer message goes to the selected seeker --}}
                <input type="hidden" name="seeker_id" value="{{ $otherUserId }}">
            @endif
            <input
                name="body"
                class="flex-1 border rounded p-2"
                placeholder="Type your message…"
                required
            />
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Send</button>
        </form>

        @error('body')
            <div class="text-red-600 text-sm">{{ $message }}</div>
        @enderror
    </div>
</x-app-layout>
