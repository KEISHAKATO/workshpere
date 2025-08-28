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

            {{-- Bio (short) --}}
            <label class="block text-sm font-medium">Bio (short)</label>
            <input name="bio"
                   value="{{ old('bio', $profile->bio) }}"
                   class="mt-1 w-full border rounded p-2"
                   placeholder="e.g. Certified electrician, 8+ yrs experience">
            @error('bio') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

            {{-- About (long) --}}
            <label class="block text-sm font-medium mt-4">About</label>
            <textarea name="about" rows="5" class="mt-1 w-full border rounded p-2"
                placeholder="Write a detailed description about your background and services.">{{ old('about', $profile->about) }}</textarea>
            @error('about') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

            {{-- Skills (comma-separated) --}}
            @php
                $skillsCsv = is_array($profile->skills ?? null) ? implode(', ', $profile->skills) : ($profile->skills ?? '');
            @endphp
            <label class="block text-sm font-medium mt-4">Skills (comma separated)</label>
            <input name="skills"
                   value="{{ old('skills', $skillsCsv) }}"
                   class="mt-1 w-full border rounded p-2"
                   placeholder="plumbing, tiling, electrical">
            @error('skills') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

            {{-- Experience --}}
            <label class="block text-sm font-medium mt-4">Experience (years)</label>
            <input type="number" name="experience_years" min="0" max="60"
                   value="{{ old('experience_years', $profile->experience_years) }}"
                   class="mt-1 w-full border rounded p-2">
            @error('experience_years') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

            {{-- Location --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium">City / Town</label>
                    <input name="location_city" value="{{ old('location_city', $profile->location_city) }}"
                           class="mt-1 w-full border rounded p-2">
                    @error('location_city') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">County</label>
                    <input name="location_county" value="{{ old('location_county', $profile->location_county) }}"
                           class="mt-1 w-full border rounded p-2">
                    @error('location_county') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Coordinates --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium">Latitude</label>
                    <input name="lat" type="text" inputmode="decimal"
                           value="{{ old('lat', $profile->lat) }}"
                           class="mt-1 w-full border rounded p-2" placeholder="-1.2921">
                    @error('lat') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Longitude</label>
                    <input name="lng" type="text" inputmode="decimal"
                           value="{{ old('lng', $profile->lng) }}"
                           class="mt-1 w-full border rounded p-2" placeholder="36.8219">
                    @error('lng') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>
            </div>

            <button class="mt-6 px-4 py-2 bg-blue-600 text-white rounded">Save</button>
        </form>
    </div>
</x-app-layout>
