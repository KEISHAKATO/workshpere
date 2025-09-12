<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Company Profile (Employer)</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-4">


        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <form method="POST" action="{{ route('employer.profile.update') }}" class="grid grid-cols-1 gap-5">
                    @csrf
                    @method('PATCH')

                    {{-- Company name --}}
                    <div class="form-control">
                        <label class="label"><span class="label-text">Company Name</span></label>
                        <input id="company_name" name="company_name" class="input input-bordered"
                               value="{{ old('company_name', $profile->company_name) }}" required>
                        <x-input-error :messages="$errors->get('company_name')" class="mt-1" />
                    </div>

                    {{-- Website --}}
                    <div class="form-control">
                        <label class="label"><span class="label-text">Website</span></label>
                        <input id="website" type="url" name="website" class="input input-bordered"
                               value="{{ old('website', $profile->website) }}" placeholder="https://example.com">
                        <x-input-error :messages="$errors->get('website')" class="mt-1" />
                    </div>

                    {{-- About --}}
                    <div class="form-control">
                        <label class="label"><span class="label-text">About</span></label>
                        <textarea id="about" name="about" class="textarea textarea-bordered" rows="5"
                                  placeholder="Describe your company and the kind of talent you hire.">{{ old('about', $profile->about) }}</textarea>
                        <x-input-error :messages="$errors->get('about')" class="mt-1" />
                    </div>

                    {{-- Location (single search field) --}}
                    <div class="form-control">
                        <label for="emp-location" class="label"><span class="label-text">Location (search)</span></label>
                        <input id="emp-location" type="text" class="input input-bordered"
                               placeholder="Start typing a city, county, address…"
                               data-gmaps="autocomplete"
                               data-country="ke"
                               data-target-city="[name='location_city']"
                               data-target-county="[name='location_county']"
                               data-target-lat="[name='lat']"
                               data-target-lng="[name='lng']"
                               autocomplete="off">
                        <label class="label"><span class="label-text-alt">Pick a suggestion to save City/County and coordinates.</span></label>
                    </div>

                    {{-- Hidden fields populated by autocomplete --}}
                    <input type="hidden" name="location_city"   value="{{ old('location_city', $profile->location_city) }}">
                    <input type="hidden" name="location_county" value="{{ old('location_county', $profile->location_county) }}">
                    <input type="hidden" name="lat"             value="{{ old('lat', $profile->lat) }}">
                    <input type="hidden" name="lng"             value="{{ old('lng', $profile->lng) }}">

                    {{-- Saved Location (summary) --}}
                    @php
                        $hasLocation = $profile->location_city || $profile->location_county || $profile->lat || $profile->lng;
                    @endphp
                    <div class="alert">
                        <div>
                            <div class="font-medium">Saved location</div>
                            @if($hasLocation)
                                <dl class="grid grid-cols-2 gap-x-6 gap-y-1 text-sm mt-2">
                                    <div><dt class="opacity-70">City</dt><dd class="font-medium">{{ $profile->location_city ?? '—' }}</dd></div>
                                    <div><dt class="opacity-70">County</dt><dd class="font-medium">{{ $profile->location_county ?? '—' }}</dd></div>
                                    <div><dt class="opacity-70">Latitude</dt><dd class="font-medium">{{ $profile->lat ?? '—' }}</dd></div>
                                    <div><dt class="opacity-70">Longitude</dt><dd class="font-medium">{{ $profile->lng ?? '—' }}</dd></div>
                                </dl>
                            @else
                                <p class="text-sm opacity-70 mt-1">No location saved yet. Use the search box above and save.</p>
                            @endif
                        </div>
                    </div>

                    <div class="form-control">
                        <button class="btn btn-primary">Save</button>
                    </div>
                </form>

                {{-- Reviews & Ratings --}}
                <div class="divider my-6">Reviews</div>
                @php
                    $full = (int) floor($avgRating ?? 0);
                    $empty = 5 - $full;
                @endphp

                <div class="flex items-center gap-3 mb-3">
                    <div class="rating rating-sm">
                        {!! str_repeat('<input type="radio" class="mask mask-star-2 bg-amber-400" checked />', $full) !!}
                        {!! str_repeat('<input type="radio" class="mask mask-star-2" />', $empty) !!}
                    </div>
                    <div class="text-sm opacity-70">
                        @if($reviewsCount > 0)
                            {{ number_format($avgRating ?? 0, 1) }} / 5 • {{ $reviewsCount }} review{{ $reviewsCount === 1 ? '' : 's' }}
                        @else
                            No reviews yet
                        @endif
                    </div>
                </div>

                @forelse($reviews as $rev)
                    @php
                        $rFull = (int) $rev->rating;
                        $rEmpty = 5 - $rFull;
                    @endphp
                    <div class="bg-base-200 rounded-xl p-4 mb-3">
                        <div class="flex items-center justify-between">
                            <div class="font-medium">{{ $rev->title ?: 'Review' }}</div>
                            <div class="rating rating-xs">
                                {!! str_repeat('<input type="radio" class="mask mask-star-2 bg-amber-400" checked />', $rFull) !!}
                                {!! str_repeat('<input type="radio" class="mask mask-star-2" />', $rEmpty) !!}
                            </div>
                        </div>
                        @if($rev->comment)
                            <p class="mt-2">{{ $rev->comment }}</p>
                        @endif
                        <div class="mt-2 text-xs opacity-70">
                            by {{ $rev->reviewer->name ?? '—' }} • {{ optional($rev->created_at)->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <p class="opacity-70">No reviews yet.</p>
                @endforelse

                @if($reviews->hasPages())
                    <div class="mt-2">{{ $reviews->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
