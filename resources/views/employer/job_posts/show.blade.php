<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl">{{ $job->title }}</h2>

            <div class="flex items-center gap-2">
                <a class="px-3 py-2 bg-gray-100 rounded"
                   href="{{ route('employer.job_posts.edit', $job) }}">Edit</a>

                <a class="px-3 py-2 bg-gray-100 rounded"
                   href="{{ route('public.jobs.show', $job) }}">View public</a>

                <a class="px-3 py-2 bg-blue-600 text-white rounded"
                   href="{{ route('employer.applications.index', $job) }}">Applications</a>

                {{-- Chat link --}}
                <a class="px-3 py-2 bg-gray-100 rounded"
                   href="{{ route('chat.show', $job) }}">Open chat</a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto p-6 bg-white rounded-xl shadow space-y-4">
        @if(session('status'))
            <div class="p-2 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
        @endif

        {{-- Meta --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div><strong>Type:</strong> {{ ucfirst(str_replace('_',' ', $job->job_type)) }}</div>
            <div><strong>Status:</strong> {{ ucfirst($job->status) }}</div>
            <div><strong>City:</strong> {{ $job->location_city ?? '—' }}</div>
            <div><strong>County:</strong> {{ $job->location_county ?? '—' }}</div>
            <div class="sm:col-span-2">
                <strong>Pay:</strong>
                {{ $job->currency ?? 'KES' }}
                {{ $job->pay_min ? number_format($job->pay_min) : '—' }}
                –
                {{ $job->pay_max ? number_format($job->pay_max) : '—' }}
            </div>
            <div class="sm:col-span-2">
                <strong>Posted:</strong> {{ optional($job->posted_at)->toDayDateTimeString() ?? '—' }}
            </div>
        </div>

        {{-- Required skills --}}
        @if(is_array($job->required_skills) && count($job->required_skills))
            <div class="text-sm">
                <strong>Required skills:</strong>
                <div class="mt-1">
                    @foreach($job->required_skills as $s)
                        <span class="inline-block px-2 py-1 bg-gray-100 rounded mr-1 mt-1">{{ $s }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Description --}}
        <div class="prose max-w-none">
            <h3 class="font-semibold text-lg mt-2">Description</h3>
            <p class="text-gray-700 whitespace-pre-line">{{ $job->description }}</p>
        </div>
    </div>
</x-app-layout>
