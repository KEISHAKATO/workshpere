<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">My Profile</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-6 bg-white rounded-xl shadow">
        @if (session('status'))
            <div class="mb-4 p-3 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('seeker.profile.update') }}">
            @csrf
            @method('PATCH')

            <label for="bio" class="block text-sm font-medium">Bio (short)</label>
            <input id="bio" name="bio"
                   value="{{ old('bio', $profile->bio) }}"
                   class="mt-1 w-full border rounded p-2"
                   placeholder="e.g. Certified electrician, 8+ yrs experience">
            @error('bio') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

            <label for="about" class="block text-sm font-medium mt-4">About</label>
            <textarea id="about" name="about" rows="5" class="mt-1 w-full border rounded p-2"
                placeholder="Write a detailed description about your background and services.">{{ old('about', $profile->about) }}</textarea>
            @error('about') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

            @php
                $skillsCsv = is_array($profile->skills ?? null) ? implode(', ', $profile->skills) : ($profile->skills ?? '');
            @endphp
            <label for="skills" class="block text-sm font-medium mt-4">Skills (comma separated)</label>
            <input id="skills" name="skills"
                   value="{{ old('skills', $skillsCsv) }}"
                   class="mt-1 w-full border rounded p-2"
                   placeholder="plumbing, tiling, electrical">
            @error('skills') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

            <label for="experience_years" class="block text-sm font-medium mt-4">Experience (years)</label>
            <input id="experience_years" type="number" name="experience_years" min="0" max="60"
                   value="{{ old('experience_years', $profile->experience_years) }}"
                   class="mt-1 w-full border rounded p-2">
            @error('experience_years') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

            {{-- Location search (Google Places) --}}
            <div class="mt-4">
                <label for="seek-location" class="block text-sm font-medium">Location (search)</label>
                <input id="seek-location"
                    type="text"
                    class="w-full border rounded p-2"
                    placeholder="Start typing a city, county, address…"
                    data-gmaps="autocomplete"
                    data-country="ke"
                    data-target-city="[name='location_city']"
                    data-target-county="[name='location_county']"
                    data-target-lat="[name='lat']"
                    data-target-lng="[name='lng']"
                    autocomplete="off">
                <p class="text-xs text-gray-500 mt-1">Pick a suggestion to fill City/County and coordinates.</p>
            </div>

            {{-- Hidden fields (populated by autocomplete) --}}
            <input type="hidden" name="location_city" value="{{ old('location_city', $profile->location_city) }}">
            <input type="hidden" name="location_county" value="{{ old('location_county', $profile->location_county) }}">
            <input type="hidden" name="lat" value="{{ old('lat', $profile->lat) }}">
            <input type="hidden" name="lng" value="{{ old('lng', $profile->lng) }}">

            {{-- Saved location summary (read-only UI) --}}
            @php
                $city   = old('location_city', $profile->location_city);
                $county = old('location_county', $profile->location_county);
                $lat    = old('lat', $profile->lat);
                $lng    = old('lng', $profile->lng);
            @endphp
            @if($city || $county || $lat || $lng)
                <div class="mt-4 rounded-lg border p-3 bg-gray-50">
                    <div class="text-sm text-gray-600">Saved location:</div>
                    <div class="text-sm text-gray-900">
                        {{ $city ?: '—' }}{{ $city && $county ? ', ' : '' }}{{ $county ?: '' }}
                        @if($lat && $lng)
                            <span class="text-gray-500"> • {{ $lat }}, {{ $lng }}</span>
                        @endif
                    </div>
                </div>
            @endif

            <button class="mt-6 px-4 py-2 bg-blue-600 text-white rounded">Save</button>
        </form>
    </div>
</x-app-layout>
