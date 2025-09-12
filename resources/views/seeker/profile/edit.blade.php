<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">My Profile</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-4">


        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <form method="POST" action="{{ route('seeker.profile.update') }}" class="grid grid-cols-1 gap-5">
                    @csrf
                    @method('PATCH')

                    {{-- Bio --}}
                    <div class="form-control">
                        <label class="label"><span class="label-text">Bio (short)</span></label>
                        <input id="bio" name="bio" value="{{ old('bio', $profile->bio) }}"
                               class="input input-bordered" placeholder="e.g. Certified electrician, 8+ yrs experience">
                        <x-input-error :messages="$errors->get('bio')" class="mt-1" />
                    </div>

                    {{-- About --}}
                    <div class="form-control">
                        <label class="label"><span class="label-text">About</span></label>
                        <textarea id="about" name="about" rows="6" class="textarea textarea-bordered"
                                  placeholder="Write a detailed description about your background and services.">{{ old('about', $profile->about) }}</textarea>
                        <x-input-error :messages="$errors->get('about')" class="mt-1" />
                    </div>

                    {{-- Skills CSV + preview --}}
                    @php
                        $skillsCsv = is_array($profile->skills ?? null) ? implode(', ', $profile->skills) : ($profile->skills ?? '');
                    @endphp
                    <div class="form-control">
                        <label class="label"><span class="label-text">Skills (comma separated)</span></label>
                        <input id="skills" name="skills" value="{{ old('skills', $skillsCsv) }}"
                               class="input input-bordered" placeholder="plumbing, tiling, electrical">
                        <x-input-error :messages="$errors->get('skills')" class="mt-1" />
                        <div id="skills-preview" class="mt-2 flex flex-wrap gap-2"></div>
                        <p class="text-xs opacity-70 mt-1">Tip: comma or Enter to separate skills.</p>
                    </div>

                    {{-- Experience --}}
                    <div class="form-control">
                        <label class="label"><span class="label-text">Experience (years)</span></label>
                        <input id="experience_years" type="number" name="experience_years" min="0" max="60"
                               value="{{ old('experience_years', $profile->experience_years) }}"
                               class="input input-bordered w-40">
                        <x-input-error :messages="$errors->get('experience_years')" class="mt-1" />
                    </div>

                    {{-- Location search (Google Places) --}}
                    <div class="form-control">
                        <label for="seek-location" class="label"><span class="label-text">Location (search)</span></label>
                        <input id="seek-location" type="text" class="input input-bordered"
                               placeholder="Start typing a city, county, address…"
                               data-gmaps="autocomplete"
                               data-country="ke"
                               data-target-city="[name='location_city']"
                               data-target-county="[name='location_county']"
                               data-target-lat="[name='lat']"
                               data-target-lng="[name='lng']"
                               autocomplete="off">
                        <label class="label"><span class="label-text-alt">Pick a suggestion to fill City/County and coordinates.</span></label>
                    </div>

                    {{-- Hidden fields (populated by autocomplete) --}}
                    <input type="hidden" name="location_city" value="{{ old('location_city', $profile->location_city) }}">
                    <input type="hidden" name="location_county" value="{{ old('location_county', $profile->location_county) }}">
                    <input type="hidden" name="lat" value="{{ old('lat', $profile->lat) }}">
                    <input type="hidden" name="lng" value="{{ old('lng', $profile->lng) }}">

                    {{-- Saved location summary --}}
                    @php
                        $city   = old('location_city', $profile->location_city);
                        $county = old('location_county', $profile->location_county);
                        $lat    = old('lat', $profile->lat);
                        $lng    = old('lng', $profile->lng);
                    @endphp
                    @if($city || $county || $lat || $lng)
                        <div class="alert">
                            <div>
                                <div class="font-medium">Saved location</div>
                                <div class="text-sm opacity-80">
                                    {{ $city ?: '—' }}{{ $city && $county ? ', ' : '' }}{{ $county ?: '' }}
                                    @if($lat && $lng)
                                        <span class="opacity-60"> • {{ $lat }}, {{ $lng }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

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

    {{-- Tiny skills preview helper --}}
    <script>
        (function () {
            const input = document.getElementById('skills');
            const preview = document.getElementById('skills-preview');
            if (!input || !preview) return;

            function render() {
                const parts = input.value.split(',').map(s => s.trim()).filter(Boolean).slice(0, 24);
                preview.innerHTML = parts.map(s => `<span class="badge">${s}</span>`).join(' ');
            }
            input.addEventListener('input', render);
            render();
        })();
    </script>
</x-app-layout>
