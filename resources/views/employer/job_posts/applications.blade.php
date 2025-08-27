<x-app-layout>
    <div class="max-w-5xl mx-auto p-6 space-y-4">
        <h1 class="text-2xl font-semibold">Applicants for: {{ $job->title }}</h1>
        @if(session('ok')) <div class="p-2 bg-green-100">{{ session('ok') }}</div> @endif
        <ul class="space-y-3">
            @forelse($applications as $app)
                <li class="border p-3 rounded">
                    <div class="font-medium">{{ $app->seeker->name }} ({{ $app->seeker->email }})</div>
                    <div class="text-sm text-gray-600 mb-2">Status: {{ $app->status }}</div>
                    <p class="mb-2">{{ $app->cover_letter }}</p>
                    <form method="post" action="{{ route('employer.applications.updateStatus',$app) }}" class="flex gap-2">
                        @csrf @method('PUT')
                        <select name="status" class="border p-1">
                            @foreach(['pending','accepted','rejected'] as $s)
                              <option value="{{ $s }}" @selected($app->status===$s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        <button class="px-3 py-1 bg-blue-600 text-white rounded">Update</button>
                    </form>
                </li>
            @empty
                <li>No applicants yet.</li>
            @endforelse
        </ul>
        <div>{{ $applications->links() }}</div>
    </div>
</x-app-layout>
