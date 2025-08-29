<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ $job->title }}</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto p-6 bg-white rounded-xl shadow">
        <div class="text-sm text-gray-500">
            {{ $job->location_city ?? '—' }}, {{ $job->location_county ?? '—' }}
            • {{ ucfirst(str_replace('_',' ', $job->job_type)) }}
        </div>

        @if($job->pay_min || $job->pay_max)
            <div class="mt-2 font-medium">
                {{ $job->currency ?? 'KES' }}
                {{ $job->pay_min ? number_format($job->pay_min) : '—' }}
                –
                {{ $job->pay_max ? number_format($job->pay_max) : '—' }}
            </div>
        @endif

        @if(is_array($job->required_skills) && count($job->required_skills))
            <div class="mt-3">
                <span class="text-sm text-gray-600">Skills:</span>
                @foreach($job->required_skills as $skill)
                    <span class="inline-block px-2 py-1 text-xs bg-gray-100 rounded mr-1 mt-1">{{ $skill }}</span>
                @endforeach
            </div>
        @endif

        <div class="prose max-w-none mt-6">
            {!! nl2br(e($job->description)) !!}
        </div>

        <div class="mt-6">
            @auth
                @if(auth()->user()->isSeeker() || auth()->user()->isAdmin())
                    <form method="POST" action="{{ route('seeker.apply.store', $job) }}" class="space-y-3">
                        @csrf
                        <label class="block text-sm text-gray-700">Cover letter (optional)</label>
                        <textarea name="cover_letter" rows="4" class="w-full border rounded p-2"
                            placeholder="Brief note about your experience and availability...">{{ old('cover_letter') }}</textarea>
                        @error('cover_letter') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

                        <button class="px-4 py-2 bg-blue-600 text-white rounded">Apply</button>
                    </form>
                @else
                    <p class="text-sm text-gray-600">Log in as a job seeker to apply.</p>
                @endif
            @else
                <a href="{{ route('login') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Log in to apply</a>
            @endauth

            @if (session('status'))
                <div class="mt-3 p-3 bg-green-50 text-green-700 rounded">
                    {{ session('status') }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
