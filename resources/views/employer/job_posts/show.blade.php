<x-app-layout>
    <div class="max-w-5xl mx-auto p-6 space-y-4">
        @if(session('ok')) <div class="p-2 bg-green-100">{{ session('ok') }}</div> @endif

        <div class="border p-4 rounded">
            <h1 class="text-2xl font-semibold">{{ $job->title }}</h1>
            <div class="text-sm text-gray-600">{{ $job->location_city }}, {{ $job->location_county }} • {{ $job->job_type }} • {{ $job->status }}</div>
            <p class="mt-3">{{ $job->description }}</p>
            <div class="mt-2 text-sm">Skills: {{ implode(', ', (array)$job->required_skills) }}</div>
            <div class="mt-4 flex gap-3">
                <a href="{{ route('employer.job_posts.edit',$job) }}" class="text-blue-600 underline">Edit</a>
                <form method="post" action="{{ route('employer.job_posts.destroy',$job) }}">
                    @csrf @method('DELETE')
                    <button class="text-red-600 underline">Delete</button>
                </form>
            </div>
        </div>

        <div class="border p-4 rounded">
            <h2 class="text-xl font-semibold mb-2">Applications</h2>
            <a class="text-blue-600 underline" href="{{ route('employer.applications.index',$job) }}">View applicants</a>
        </div>

        <div class="border p-4 rounded">
            <h2 class="text-xl font-semibold mb-2">Message an applicant</h2>
            <form method="post" action="{{ route('messages.store',$job) }}" class="space-y-2">
                @csrf
                <input name="receiver_id" class="border p-2 w-full" placeholder="Receiver user ID">
                <textarea name="content" class="border p-2 w-full" placeholder="Write a message"></textarea>
                <button class="bg-blue-600 text-white px-3 py-1 rounded">Send</button>
            </form>
        </div>
    </div>
</x-app-layout>
