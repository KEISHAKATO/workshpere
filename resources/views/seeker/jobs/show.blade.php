<x-app-layout>
    <div class="max-w-3xl mx-auto p-6 space-y-4">
        @if(session('ok')) <div class="p-2 bg-green-100">{{ session('ok') }}</div> @endif
        <div class="border p-4 rounded">
            <h1 class="text-2xl font-semibold">{{ $job->title }}</h1>
            <div class="text-sm text-gray-600">{{ $job->location_city }}, {{ $job->location_county }} • {{ $job->job_type }}</div>
            <p class="mt-3">{{ $job->description }}</p>
            <form method="post" action="{{ route('seeker.apply.store',$job) }}" class="mt-4 space-y-2">
                @csrf
                <textarea name="cover_letter" class="w-full border p-2" placeholder="Short note (optional)"></textarea>
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Apply</button>
            </form>
        </div>
    </div>
</x-app-layout>
