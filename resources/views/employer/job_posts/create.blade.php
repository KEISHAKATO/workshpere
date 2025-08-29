<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Post a Job</h2></x-slot>

    <div class="max-w-3xl mx-auto p-6 bg-white rounded-xl shadow">
        <form method="POST" action="{{ route('employer.job_posts.store') }}" class="space-y-4">
            @csrf
            @include('employer.job_posts.partials.form-fields', ['job' => null])
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Create</button>
        </form>
    </div>
</x-app-layout>
