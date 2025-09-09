<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Company Profile (Employer)</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-6 bg-white rounded-xl shadow">
        @if (session('status'))
            <div class="mb-4 p-3 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('employer.profile.update') }}" class="space-y-6">
            @csrf
            @method('PATCH')

            {{-- Company name --}}
            <div>
                <label for="company_name" class="block text-sm font-medium">Company Name</label>
                <input id="company_name" name="company_name" class="mt-1 w-full border rounded p-2"
                       value="{{ old('company_name', $profile->company_name) }}" required>
                @error('company_name') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Website --}}
            <div>
                <label for="website" class="block text-sm font-medium">Website</label>
                <input id="website" type="url" name="website" class="mt-1 w-full border rounded p-2"
                       value="{{ old('website', $profile->website) }}" placeholder="https://example.com">
                @error('website') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- About --}}
            <div>
                <label for="about" class="block text-sm font-medium">About</label>
                <textarea id="about" name="about" class="mt-1 w-full border rounded p-2" rows="4"
                          placeholder="Describe your company and the kind of talent you hire.">{{ old('about', $profile->about) }}</textarea>
                @error('about') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Location (single search field) --}}
            <div>
                <label for="emp-location" class="block text-sm font-medium">Location (search)</label>
                <input id="emp-location"
                       type="text"
                       class="mt-1 w-full border rounded p-2"
                       placeholder="Start typing a city, county, address…"
                       data-gmaps="autocomplete"
                       data-country="ke"
                       data-target-city="[name='location_city']"
                       data-target-county="[name='location_county']"
                       data-target-lat="[name='lat']"
                       data-target-lng="[name='lng']"
                       autocomplete="off">
                <p class="text-xs text-gray-500 mt-1">Pick a suggestion to save City/County and coordinates.</p>
            </div>

            {{-- Hidden fields populated by autocomplete --}}
            <input type="hidden" name="location_city"   value="{{ old('location_city', $profile->location_city) }}">
            <input type="hidden" name="location_county" value="{{ old('location_county', $profile->location_county) }}">
            <input type="hidden" name="lat"             value="{{ old('lat', $profile->lat) }}">
            <input type="hidden" name="lng"             value="{{ old('lng', $profile->lng) }}">

            {{-- Saved Location (read-only summary) --}}
            <div class="rounded-lg border p-4 bg-gray-50">
                <div class="text-sm font-medium text-gray-700 mb-2">Saved location</div>
                @php
                    $hasLocation = $profile->location_city || $profile->location_county || $profile->lat || $profile->lng;
                @endphp

                @if($hasLocation)
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 text-sm">
                        <div>
                            <dt class="text-gray-500">City</dt>
                            <dd class="font-medium text-gray-900">{{ $profile->location_city ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">County</dt>
                            <dd class="font-medium text-gray-900">{{ $profile->location_county ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Latitude</dt>
                            <dd class="font-medium text-gray-900">{{ $profile->lat ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Longitude</dt>
                            <dd class="font-medium text-gray-900">{{ $profile->lng ?? '—' }}</dd>
                        </div>
                    </dl>
                @else
                    <p class="text-sm text-gray-500">No location saved yet. Use the search box above and save.</p>
                @endif
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
        </form>
    </div>
</x-app-layout>
