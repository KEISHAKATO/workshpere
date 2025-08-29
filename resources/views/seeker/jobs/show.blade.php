<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">{{ $job->title }}</h2></x-slot>

    <div class="max-w-3xl mx-auto p-6 bg-white rounded-xl shadow space-y-4">
        <p class="text-gray-700 whitespace-pre-line">{{ $job->description }}</p>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><strong>Type:</strong> {{ str_replace('_',' ', $job->job_type) }}</div>
            <div><strong>County:</strong> {{ $job->location_county }}</div>
            <div><strong>City:</strong> {{ $job->location_city }}</div>
            <div><strong>Pay:</strong> {{ $job->currency }} {{ $job->pay_min }} – {{ $job->pay_max }}</div>
        </div>

        @if(is_array($job->required_skills))
            <div class="text-sm">
                <strong>Required skills:</strong>
                @foreach($job->required_skills as $s)
                    <span class="inline-block px-2 py-1 bg-gray-100 rounded mr-1 mt-1">{{ $s }}</span>
                @endforeach
            </div>
        @endif

        {{-- You can wire Apply button later (we already have route names) --}}
        {{-- <form method="POST" action="{{ route('seeker.apply.store', $job) }}">@csrf <button class="px-4 py-2 bg-blue-600 text-white rounded">Apply</button></form> --}}
    </div>
</x-app-layout>
