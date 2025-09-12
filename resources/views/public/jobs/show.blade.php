<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">{{ $job->title }}</h2></x-slot>

    <div class="max-w-3xl mx-auto p-4">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body space-y-4">
                <p class="opacity-90 whitespace-pre-line">{{ $job->description }}</p>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="opacity-70">Type:</span> <span class="font-medium">{{ str_replace('_',' ', $job->job_type) }}</span></div>
                    <div><span class="opacity-70">County:</span> <span class="font-medium">{{ $job->location_county }}</span></div>
                    <div><span class="opacity-70">City:</span> <span class="font-medium">{{ $job->location_city }}</span></div>
                    <div><span class="opacity-70">Pay:</span>
                        <span class="font-medium">{{ $job->currency }} {{ $job->pay_min }} – {{ $job->pay_max }}</span>
                    </div>
                </div>

                @if(is_array($job->required_skills))
                    <div class="text-sm">
                        <div class="font-semibold">Required skills</div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($job->required_skills as $s)
                                <span class="badge">{{ $s }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
