<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Edit Job</h2></x-slot>

    <div class="max-w-3xl mx-auto p-6 bg-white rounded-xl shadow">
        <form method="POST" action="{{ route('employer.job_posts.update', $job) }}" class="space-y-4">
            @csrf @method('PUT')
            @include('employer.job_posts.partials.form-fields', ['job' => $job])
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
        </form>

        <form method="POST" action="{{ route('employer.job_posts.destroy', $job) }}" class="mt-6">
            @csrf @method('DELETE')
            <button class="px-4 py-2 bg-red-600 text-white rounded" onclick="return confirm('Delete this job?')">Delete</button>
        </form>
    </div>
</x-app-layout>
